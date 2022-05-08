<?php

namespace App\Http\Controllers;


use App\Repository\AdminRepository;
use App\Services\JwtService;
use App\Utils\BaseUtil;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        AdminRepository $adminRepository,
        JwtService $jwtService
    ) {
        $this->adminRepository = $adminRepository;
        $this->jwtService = $jwtService;
    }

    public function login(Request $request)
    {
        $all = $request->all();

        if (empty($all["account"])) {
            return $this->return(400, "请输入帳號");
        }

        if (empty($all["password"])) {
            return $this->return(400, "请输入密码");
        }

        if (strlen($all["password"]) < 6 || strlen($all["password"]) > 32) {
            return $this->return(400, "密码必须6-32字符");
        }

        $result = $this->adminRepository->getAdminByAccount($all["account"]);

        if (empty($result)) {
            return $this->return(400, "用户名有误，请核对");
        }

        if (md5($all["password"]) != $result["password"]) {
            return $this->return(400, "密码有误，请核对");
        }

        unset($result["password"]);
        $result["token"] = $this->jwtService->createToken($result["id"]);
        return $this->return(200, $result);
    }
}
