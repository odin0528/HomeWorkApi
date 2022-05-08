<?php

namespace App\Utils;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtToken
{
    static public function createToken($id)
    {
        $key = env("JWT_SECRET");
        $time = time();
        $payload = [
            // 'iss' => 'http://www.helloweba.net', //簽發者 可選
            // 'aud' => 'http://www.helloweba.net', //接收該JWT的一方，可選
            'iat' => $time, //簽發時間
            'nbf' => $time, //(Not Before)：某個時間點後才能訪問，比如設置time+30，表示當前時間30秒後才能使用
            'exp' => $time + 3600 * 24 * 14, //過期時間,這裡設置2個小時
            'userid' => $id,
        ];

        return JWT::encode($payload, $key, 'HS256'); //輸出Token
    }

    static public function decodeToken($jwt)
    {
        $key = env("JWT_SECRET");

        try {
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
            return $decoded->userid;
        } catch (\Exception $e) {
            return false;
        }
    }
}
