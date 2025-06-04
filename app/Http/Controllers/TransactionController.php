<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected AuthorizationService $authorization;
    protected NotificationService $notification;
    public function __construct(AuthorizationService $authorization, NotificationService $notification)
    {
        $this->authorization = $authorization;
        $this->notification = $notification;
    }
    public function transfer(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'payer_id' => 'required|exists:users,id',
            'payee_id' => 'required|exists:users,id|different:payer_id',
            'value' => 'required|numeric|min:0.01',
        ]);

        $payer = User::find($validated['payer_id']);
        $payee = User::find($validated['payee_id']);

        if ($payer->shopkeeper) {
            return response()->json([
                'message' => 'Lojistas não podem efetuar transferências'
            ], 400);
        }

        if ($payer->wallet->balance < $validated['value']) {
            return response()->json([
                'message' => 'Saldo insuficiente para transação'
            ], 400);
        }

        if (!$this->authorization->isAuthorized()) {
            return response()->json([
                'message' => 'Erro ao efetuar a transferência, tente novamente mais tarde'
            ], 503);
        }

        $transaction = null;

        \DB::transaction(function () use (&$transaction, $payer, $payee, $validated) {
            $payer->wallet->decrement('balance', $validated['value']);
            $payee->wallet->increment('balance', $validated['value']);
            $transaction = Transaction::create([
                'payer_id' => $payer->id,
                'payee_id' => $payee->id,
                'value' => $validated['value'],
            ]);
        });

        $this->notification->notify([
            'transaction_id' => $transaction->id,
            'payer' => $payer->name,
            'payee' => $payee->name,
            'value' => $transaction->value,
            'timestamp' => now()->toDateTimeString(),
        ]);

        return response()->json([
            'message' => 'Transferência realizada com sucesso de ' . $payer->name . ' para ' . $payee->name .
                ' no valor de R$' . number_format($validated['value'], 2, ',', '.'),
        ], 201);
    }
}
