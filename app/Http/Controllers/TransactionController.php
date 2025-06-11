<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Mail\TransferEmail;
use App\Models\User;
use App\Models\Transaction;
use App\Services\AuthorizationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    public function __construct(
        private AuthorizationService $authorization,
        private NotificationService $notification
    ) {
    }
    public function transfer(TransferRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $payer = User::find($validated['payer_id']);
        $payee = User::find($validated['payee_id']);

        if ($payer->shopkeeper) {
            return response()->json([
                'message' => 'Lojistas não podem efetuar transferências'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($payer->wallet->balance < $validated['value']) {
            return response()->json([
                'message' => 'Saldo insuficiente para transação'
            ], Response::HTTP_BAD_REQUEST);
        }

        if (!$this->authorization->isAuthorized()) {
            return response()->json([
                'message' => 'Erro ao efetuar a transferência, tente novamente mais tarde'
            ], Response::HTTP_SERVICE_UNAVAILABLE);
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

        Mail::to($payer->email)->queue(new TransferEmail($payer, $payee, $validated['value']));

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
        ], Response::HTTP_CREATED);
    }
}
