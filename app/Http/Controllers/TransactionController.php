<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class TransactionController extends Controller
{
    public function __construct(
        private TransferService $transferService
    ) {}

    public function transfer(TransferRequest $request): JsonResponse
    {
        $validated = $request->validated();

        try {
            $transaction = $this->transferService->transfer(
                payerId: $validated['payer_id'],
                payeeId: $validated['payee_id'],
                value: $validated['value']
            );

            return response()->json([
                'message' => 'Transferência realizada com sucesso',
                'transaction' => $transaction
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}

