<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class TransferFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_transfer_success()
    {
        $payer = User::factory()->create(['shopkeeper' => false]);
        $payee = User::factory()->create();

        $payer->wallet()->update(['balance' => 100.00]);
        $payee->wallet()->create(['balance' => 0]);

        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response(['data' => ['authorization' => true]], 200),
            'https://util.devi.tools/api/v1/notify' => Http::response([], 200),
        ]);

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 50.00,
        ];

        $response = $this->postJson('/api/transfer', $payload);

        $response->assertStatus(202);
        $response->assertJson([
            'message' => "Transferência realizada com sucesso de {$payer->name} para {$payee->name} no valor de R$50,00",
        ]);

        $this->assertDatabaseHas('wallet', [
            'user_id' => $payer->id,
            'balance' => 50.00,
        ]);

        $this->assertDatabaseHas('wallet', [
            'user_id' => $payee->id,
            'balance' => 50.00,
        ]);

        $this->assertDatabaseHas('transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 50.00,
        ]);
    }

    public function test_transfer_fails_if_payer_is_shopkeeper()
    {
        $payer = User::factory()->create(['shopkeeper' => true]);
        $payee = User::factory()->create();

        $payer->wallet()->create(['balance' => 100]);
        $payee->wallet()->create(['balance' => 0]);

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 10.00,
        ];

        $response = $this->postJson('/api/transfer', $payload);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Lojistas não podem efetuar transferências']);
    }

    public function test_transfer_fails_if_insufficient_balance()
    {
        $payer = User::factory()->create(['shopkeeper' => false]);
        $payee = User::factory()->create();

        $payer->wallet()->create(['balance' => 10]);
        $payee->wallet()->create(['balance' => 0]);

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 20.00,
        ];

        $response = $this->postJson('/api/transfer', $payload);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Saldo insuficiente para transação']);
    }

    public function test_transfer_fails_if_authorization_fails()
    {
        $payer = User::factory()->create(['shopkeeper' => false]);
        $payee = User::factory()->create();

        $payer->wallet()->update(['balance' => 100]);
        $payee->wallet()->create(['balance' => 0]);

        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response(['data' => ['authorization' => false]], 200),
        ]);

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 10.00,
        ];

        $response = $this->postJson('/api/transfer', $payload);

        $response->assertStatus(503);
        $response->assertJson(['message' => 'Erro ao efetuar a transferência, tente novamente mais tarde']);
    }
}
