<?php

namespace App\Services;

use App\Jobs\TransferCreatedJob;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\WalletRepository;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private WalletRepository $walletRepository,
        private AuthorizationService $authorization,
        private NotificationService $notification
    ) {}

    public function transfer(int $payerId, int $payeeId, float $value): Transaction
    {
        $payer = User::findOrFail($payerId);
        $payee = User::findOrFail($payeeId);

        if ($payer->id === $payee->id) {
            throw new \Exception("Não pode transferir para si mesmo");
        }

        if ($payer->shopkeeper) {
            throw new \Exception("Lojistas não podem efetuar transferências");
        }

        if ($payer->wallet->balance < $value) {
            throw new \Exception("Saldo insuficiente");
        }

        if (!$this->authorization->isAuthorized()) {
            throw new \Exception("Serviço de autorização falhou");
        }

        DB::beginTransaction();

        try {
            $transaction = $this->walletRepository->createTransfer($payer, $payee, $value);

            DB::commit();

            TransferCreatedJob::dispatch($payer, $payee, $value);

            $this->notification->notify([
                'transaction_id' => $transaction->id,
                'payer' => $payer->name,
                'payee' => $payee->name,
                'value' => $value,
                'timestamp' => now()->toDateTimeString(),
            ]);

            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
