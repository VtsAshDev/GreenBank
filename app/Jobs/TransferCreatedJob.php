<?php

namespace App\Jobs;

use App\Mail\TransferEmail;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class TransferCreatedJob implements ShouldQueue
{

    use Queueable, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $payer, private User $payee , private float $value )
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->payer->email)->queue(new TransferEmail($this->payer, $this->payee, $this->value));
    }
}
