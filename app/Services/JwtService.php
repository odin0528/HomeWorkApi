<?php

namespace App\Services;

use App\Utils\JwtToken;

class JwtService
{
    public static function createToken($id)
    {
        return JwtToken::createToken($id);
    }

    public static function verifyToken($request)
    {
        $header = $request->header('Authorization', '');

        if ($header == '') {
            return false;
        }

        $userid = JwtToken::decodeToken($header);

        if (!$userid) {
            return false;
        }

        return $userid;
    }
}
