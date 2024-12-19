<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureJsonApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->isJson())
            return (new ErrorResponse(Response::HTTP_BAD_REQUEST,
                'Les requêtes destinées vers cette API doivent être au format "JSON".')
            )->toJson();

        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }
}
