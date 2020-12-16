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
class CheckQueryHeaders
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
        if ($request->header('Content-Type') !== 'application/json') {
            return $this->_sendError('Передано недопустимое значение заголовка Content-Type');
        }

        if ($request->header('Accept') !== 'application/json') {
            return $this->_sendError('Передано недопустимое значение заголовка Accept');
        }

        return $next($request);
    }

    /**
     * @param $text
     *
     * @return Response
     */
    private function _sendError($text)
    {
        $response = new Response(
            ApiResponseDTO::error($text, [], true),
            200,
            ['Content-Type' => 'application/json']
        );

        return $response->send();
    }
}
