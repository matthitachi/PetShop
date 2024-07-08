<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FormatResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $formattedResponse = [
                'success' => isset($data['success']) ? $data['success'] : 0,
                'data' => isset($data['data']) ? $data['data'] : [],
                'error' => isset($data['error']) ? $data['error'] : '',
                'errors' => isset($data['errors']) ? $data['errors'] : [],
                'trace' => isset($data['trace']) ? $data['trace'] : [],
            ];
            $response->setData($formattedResponse);
        }

        return $response;
    }
}
