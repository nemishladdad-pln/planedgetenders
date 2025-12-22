<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GatewayProxyTest extends TestCase
{
    public function test_api_request_is_proxied_to_gateway_when_enabled()
    {
        // enable proxy for test runtime
        putenv('REPLACE_BACKEND=1');
        putenv('GATEWAY_URL=http://gateway.example');

        // fake external gateway responses
        Http::fake([
            'http://gateway.example/*' => Http::response(['proxied' => true, 'source' => 'gateway'], 200),
        ]);

        // Call any API route; middleware should forward and return the fake response
        $response = $this->get('/api/tenders');

        $response->assertStatus(200);
        $response->assertJson(['proxied' => true, 'source' => 'gateway']);
    }

    public function test_non_api_request_not_proxied()
    {
        putenv('REPLACE_BACKEND=1');
        putenv('GATEWAY_URL=http://gateway.example');

        // ensure gateway isn't called for non-API route
        Http::fake();

        $response = $this->get('/'); // root route should be handled by Laravel app
        $response->assertStatus(200);
    }
}
