<?php

namespace Tests\Unit;

use App\Services\AuthorizationService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AuthorizationServiceTest extends TestCase
{
    /** @test */
    public function authorizationSucessful()
    {
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response([
                'data' => ['authorization' => true]
            ], 200)
        ]);

        $service = new AuthorizationService();
        $result = $service->isAuthorized();
        $this->assertTrue($result);
    }

    /** @test */
    public function authorizationFailed()
    {
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response([
                'data' => ['authorization' => false]
            ], 200)
        ]);

        $service = new AuthorizationService();
        $result = $service->isAuthorized();

        $this->assertFalse($result);
    }

    /** @test */
    public function authorizationRequestFailed()
    {
        Http::fake([
            'https://util.devi.tools/api/v2/authorize' => Http::response([], 500)
        ]);

        $service = new AuthorizationService();
        $result = $service->isAuthorized();

        $this->assertFalse($result);
    }
}
