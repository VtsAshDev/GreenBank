<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notify(array $data): bool
    {
        try {

            $response = Http::timeout(5)
                ->post('https://util.devi.tools/api/v1/notify', $data);
            if($response->successful()) {
                return true;
            }
            $this->fallback($data, 'Erro no Envio da Notificação' . $response->status());
            return false;

        } catch (\Exception $e) {

            $this->fallback($data, $e->getMessage());
            return false;

        }

    }

    private function fallback(array $data, string $message): void
    {
        Log::error("Falha no envido da notificação",[
            'data' => $data,
            'message' => $message,
        ]);
    }
}
