<?php

namespace App\Http\Middleware;

use App\Http\Responses\ErrorResponseContent;
use App\Models\Token;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyBearerToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (empty($token))
            return (new ErrorResponseContent(Response::HTTP_UNAUTHORIZED, 'Le token "Bearer" est manquant'))
                ->createJsonResponse();

        $tokenRecord = Token::where('valeur', $token)
            ->orderBy('date_heure_creation', 'desc')
            ->first();
        if (!$tokenRecord)
            return (new ErrorResponseContent(Response::HTTP_UNAUTHORIZED, 'Token invalide ou expiré'))
                ->createJsonResponse();

        $request->merge(['utilisateur' => $tokenRecord->utilisateur]);
        return $next($request);
    }
}
