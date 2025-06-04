<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthorizationService
{
    public function isAuthorized():bool
    {
        try {
            $response = Http::timeout(5)->get('https://util.devi.tools/api/v2/authorize');

            if (!$response->successful()) {

                return false;

            }

            return filter_var($response->json('data.authorization'), FILTER_VALIDATE_BOOLEAN);

        } catch (\Exception $e) {

            return false;

        }
    }
}
