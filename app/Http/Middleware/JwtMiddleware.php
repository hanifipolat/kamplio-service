<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth as JWTAuth;
use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use function PHPUnit\Framework\isNull;


class JwtMiddleware extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                throw new Exception('Kullanıcı Bulunamadı.');
            }
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json([
                        'data' => null,
                        'status' => false,
                        'err_' => [
                            'message' => 'Token Geçersiz',
                            'code' => 1
                        ]
                    ]
                );
            } else {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                    return response()->json([
                            'data' => null,
                            'status' => false,
                            'err_' => [
                                'message' => 'Token süresi doldu',
                                'code' => 1
                            ]
                        ]
                    );
                } else {
                    if ($e->getMessage() === 'Kullanıcı Bulunamadı.') {
                        return response()->json([
                                "data" => null,
                                "status" => false,
                                "err_" => [
                                    "message" => $e->getMessage(),
                                    "code" => 1
                                ]
                            ]
                        );
                    }
                    return response()->json([
                            'data' => null,
                            'status' => false,
                            'err_' => [
                                'message' => 'Token geçerli değil',
                                'code' => 1
                            ]
                        ]
                    );
                }
            }
        }
        return $next($request);
    }
}
