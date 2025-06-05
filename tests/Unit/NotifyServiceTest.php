<?php

namespace Tests\Unit;

use App\Services\NotificationService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    /** @test */
    public function notifySuccessful()
    {
        Http::fake([
            'https://util.devi.tools/api/v1/notify' => Http::response([], 200),
        ]);

        $service = new NotificationService();

        $result = $service->notify(['any' => 'data']);

        $this->assertTrue($result);
    }

    /** @test */
    public function notifyFails()
    {
        Http::fake([
            'https://util.devi.tools/api/v1/notify' => Http::response([], 500),
        ]);

        Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
            return str_contains($message, 'Falha no envido da notificação') &&
                isset($context['data']) &&
                isset($context['message']);
        });

        $service = new NotificationService();

        $result = $service->notify(['any' => 'data']);

        $this->assertFalse($result);
    }

    /** @test */
    public function notifyError()
    {
        Http::fake([
            'https://util.devi.tools/api/v1/notify' => fn() => throw new \Exception('Timeout'),
        ]);

        Log::shouldReceive('error')->once()->withArgs(function ($message, $context) {
            return str_contains($message, 'Falha no envido da notificação') &&
                $context['message'] === 'Timeout';
        });

        $service = new NotificationService();

        $result = $service->notify(['any' => 'data']);

        $this->assertFalse($result);
    }
}
