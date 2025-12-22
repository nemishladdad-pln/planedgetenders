<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ProxyToGateway
{
    /**
     * Intercept API requests and proxy to external gateway when enabled.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only proxy if enabled and request path begins with /api
        if (!env('REPLACE_BACKEND', false)) {
            return $next($request);
        }

        $uri = $request->getRequestUri(); // includes query string
        $gateway = rtrim(env('GATEWAY_URL', ''), '/');
        if (empty($gateway) || !str_starts_with($request->getPathInfo(), '/api')) {
            // nothing to do
            return $next($request);
        }

        $target = $gateway . $uri;

        // Build headers to forward (exclude host-related headers)
        $forwardHeaders = [];
        foreach ($request->headers->all() as $k => $vals) {
            if (in_array(strtolower($k), ['host', 'content-length'])) continue;
            $forwardHeaders[$k] = implode(', ', $vals);
        }

        // Prepare options: raw body for JSON/text; multipart/form-data for files will be forwarded as raw body
        $body = $request->getContent();

        try {
            $method = strtoupper($request->method());
            $resp = Http::withHeaders($forwardHeaders)->send($method, $target, [
                'body' => $body,
                // keep default timeout
            ]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Gateway unreachable', 'message' => $e->getMessage()], Response::HTTP_BAD_GATEWAY);
        }

        // Map response headers (flatten arrays)
        $respHeaders = [];
        foreach ($resp->headers() as $hk => $hv) {
            if (is_array($hv)) {
                $respHeaders[$hk] = implode(', ', $hv);
            } else {
                $respHeaders[$hk] = $hv;
            }
        }

        return response($resp->body(), $resp->status())->withHeaders($respHeaders);
    }
}
