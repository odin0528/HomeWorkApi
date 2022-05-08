<?php

namespace App\Utils;

class BaseUtil
{
    static public function isMobile($mobile)
    {
        if (preg_match("/^1[34578]\d{9}$/", $mobile)) {
            return true;
        }
        return false;
    }

    /**
     * 获取客户端IP地址
     * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
     * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
     * @return mixed
     */
    static function getClientIp($type = 0, $adv = false)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) return $ip[$type];
        if ($adv) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $pos = array_search('unknown', $arr);
                if (false !== $pos) unset($arr[$pos]);
                $ip = trim($arr[0]);
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = '0.0.0.0';
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

    static function encodeId($id)
    {
        $str = substr(md5($id . env('IDKEY')), 0, 6);
        return $id . '@' . $str;
    }

    static function decodeId($id)
    {
        $data = explode('@', $id);

        if (count($data) != 2) {
            return false;
        }

        $str = substr(md5($data[0] . env('IDKEY')), 0, 6);

        if ($str != $data[1]) {
            return false;
        }

        return $data[0];
    }

    static function filterHtml($string)
    {
        $string = htmlspecialchars_decode($string);
        $rep = ['&nbsp;'];
        $to = [' '];
        $string = str_replace($rep, $to, $string);
        return $string;
    }

    static function filterEmoji($string)
    {
        $string = preg_replace_callback(
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $string
        );
        return $string;
    }

    static function base64Analysis($base64)
    {
        $image_data = explode(',', $base64);
        $file = [];

        switch ($image_data[0]) {
            case 'data:image/gif;base64':
                $file['ext'] = 'gif';
                break;
            case 'data:image/png;base64':
                $file['ext'] = 'png';
                break;
            case 'data:image/jpeg;base64':
                $file['ext'] = 'jpg';
                break;
            default:
                $file['ext'] = 'jpg';
        }

        $imageString = str_replace('=', '', $image_data[1]);
        $file['image_string'] = $imageString;
        return $file;
    }

    static function base64Decode($imageString)
    {
        return base64_decode($imageString);
    }

    static function checkFileSize2M($imageString)
    {
        $image_len = strlen($imageString);
        $file_size = $image_len - ($image_len / 8) * 2;
        $kb = intval($file_size / 1024);

        if ($kb > (1024 * 1024 * 2)) {
            return true;
        }

        return false;
    }
}
