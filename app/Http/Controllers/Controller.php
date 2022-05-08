<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function return($code, $data = null)
    {
        return response()->json([
            'code' => $code,
            'msg' => trans("returnMsg.$code"),
            'data' => $data,
        ])->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
