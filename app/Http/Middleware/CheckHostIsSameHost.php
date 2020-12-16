<?php

namespace App\Http\Middleware;

use App\Api\ApiResponseDTO;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CheckQueryHeaders
 *
 * @package App\Http\Middleware
 */
class CheckHostIsSameHost
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
        $referer = parse_url(request()->headers->get('referer'), PHP_URL_HOST);
        $host = parse_url(url('/'), PHP_URL_HOST);

        if ($referer !== $host) {
            $response = new Response(
                ApiResponseDTO::error(
                    'У вас нет доступа к данному ресурсу',
                    [
                        'referer' => $referer,
                        'host'    => $host,
                    ],
                    true
                ),
                200,
                ['Content-Type' => 'application/json']
            );

            return $response->send();
        }

        return $next($request);
    }
}
