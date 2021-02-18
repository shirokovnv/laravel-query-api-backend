<?php

namespace Shirokovnv\LaravelQueryApiBackend\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Str;

/**
 * Class ClientRequestId.
 */
class ClientRequestId
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestId = Str::uuid();
        $request->merge(['client_request_id' => $requestId]);

        $response = $next($request);
        $response->headers->set('Client-Request-Id', $requestId, false);

        return $response;
    }
}
