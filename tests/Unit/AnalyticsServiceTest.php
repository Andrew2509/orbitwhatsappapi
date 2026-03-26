<?php

namespace Tests\Unit;

use App\Services\AnalyticsService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    public function test_send_event_payload_structure()
    {
        Http::fake();

        // Mock consent cookie
        request()->cookies->set('CookieConsent', '{preferences:true,statistics:true,marketing:true}');

        $service = new AnalyticsService();
        $service->sendEvent('test_event', ['param1' => 'value1'], 'test_client_id', 'test_user_id');

        Http::assertSent(function ($request) {
            $url = $request->url();
            $body = json_decode($request->body(), true);

            return str_contains($url, 'https://www.google-analytics.com/mp/collect') &&
                   $body['client_id'] === 'test_client_id' &&
                   $body['user_id'] === 'test_user_id' &&
                   $body['events'][0]['name'] === 'test_event' &&
                   $body['events'][0]['params']['param1'] === 'value1';
        });
    }

    public function test_does_not_send_event_if_consent_declined()
    {
        Http::fake();

        // Mock consent cookie with statistics: false
        request()->cookies->set('CookieConsent', '{preferences:true,statistics:false,marketing:true}');

        $service = new AnalyticsService();
        $result = $service->sendEvent('test_event', [], 'test_client_id');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_does_not_send_event_if_cookie_missing()
    {
        Http::fake();

        // Ensure no cookie is set
        request()->cookies->remove('CookieConsent');

        $service = new AnalyticsService();
        $result = $service->sendEvent('test_event', [], 'test_client_id');

        $this->assertFalse($result);
        Http::assertNothingSent();
    }

    public function test_sends_event_if_no_consent_required()
    {
        Http::fake();

        // Mock consent cookie with -1 (no consent required)
        request()->cookies->set('CookieConsent', '-1');

        $service = new AnalyticsService();
        $result = $service->sendEvent('test_event', [], 'test_client_id');

        $this->assertTrue($result);
        Http::assertSentCount(1);
    }
}
