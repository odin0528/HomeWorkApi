<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Services\JwtService;

class Authenticate extends Middleware
{

    public function __construct(Auth $auth, JwtService $jwtService)
    {
        $this->auth = $auth;
        $this->jwtService = $jwtService;
    }

    protected function authenticate($request, array $guards)
    {
        $userid = $this->jwtService->verifyToken($request);

        var_dump($userid);

        $this->unauthenticated($request, $guards);
    }

    protected function unauthenticated($request, array $guards)
    {
        return response()->json(
            ['message' => 'User Token not found 2 !'], 404
        );
    }
}
