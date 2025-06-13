<?php

namespace App\Repositories;

use App\Http\Requests\TransferRequest;
use App\Models\Transaction;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class WalletRepository
{
    public function createTransfer(User $payer, User $payee, float $value): Transaction
    {
       $payer->wallet()->decrement('balance', $value);
       $payee->wallet()->increment('balance', $value);


       return Transaction::create([
           'payer_id' => $payer->id,
           'payee_id' => $payee->id,
           'value' => $value,
       ]);
    }
}
