<?php

namespace App\Http\Middleware;

use App\Services\JwtService;
use Closure;

class VerifyJWT
{
    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle($request, Closure $next)
    {
        $userid = $this->jwtService->verifyToken($request);
        if($userid){
            $request['adminId'] = $userid;
            return $next($request);
        }else{
            return response()->json(
                ['message' => 'User Token not found 2 !'], 404
            );
        }
    }
}