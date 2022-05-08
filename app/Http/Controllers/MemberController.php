<?php

namespace App\Http\Controllers;

use App\Repository\ChatCommentRepository;
use App\Repository\ChatRepository;
use App\Repository\MemberRepository;
use App\Repository\MyFavoriteRepository;
use App\Repository\MyFocusTopicRepository;
use App\Repository\MyPraiseRepository;
use App\Repository\RegionRepository;
use App\Repository\TypeRepository;
use App\Repository\UserDiyShowRepository;
use App\Services\JwtService;
use App\Utils\BaseUtil;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function __construct(
        ChatCommentRepository $chatCommentRepository,
        ChatRepository $chatRepository,
        MemberRepository $memberRepository,
        MyFavoriteRepository $myFavoriteRepository,
        MyFocusTopicRepository $myFocusTopicRepository,
        MyPraiseRepository $myPraiseRepository,
        RegionRepository $regionRepository,
        TypeRepository $typeRepository,
        UserDiyShowRepository $userDiyShowRepository,
        JwtService $jwtService
    ) {
        $this->chatCommentRepository = $chatCommentRepository;
        $this->chatRepository = $chatRepository;
        $this->memberRepository = $memberRepository;
        $this->myFavoriteRepository = $myFavoriteRepository;
        $this->myFocusTopicRepository = $myFocusTopicRepository;
        $this->myPraiseRepository = $myPraiseRepository;
        $this->regionRepository = $regionRepository;
        $this->typeRepository = $typeRepository;
        $this->userDiyShowRepository = $userDiyShowRepository;
        $this->jwtService = $jwtService;
    }

    public function register(Request $request)
    {
        $all = $request->all();

        if (empty($all["name"])) {
            return $this->return(400, "用户名不能为空");
        }

        $regex = "/^([0-9A-Za-z]+)$/";

        if (!preg_match($regex, $all["name"])) {
            return $this->return(400, "用户名格式不正确");
        }

        if (empty($all["nickname"])) {
            return $this->return(400, "暱稱不能为空");
        }

        if (empty($all["password"])) {
            return $this->return(400, "密码不能为空");
        }

        if (strlen($all["password"]) < 6 || strlen($all["password"]) > 32) {
            return $this->return(400, "密码必须6-32字符");
        }

        if (!empty($all["mobile"])) {
            if (!BaseUtil::isMobile($all["mobile"])) {
                return $this->return(400, "手机号码格式不正确");
            }
        }

        if ($this->memberRepository->getMemberByNickname($all["nickname"])) {
            return $this->return(400, "该暱稱已存在");
        }

        $result = $this->memberRepository->getMemberByName($all["name"]);

        if ($result) {
            return $this->return(400, "该用户名已存在");
        }

        $params = [
            "name" => $all["name"],
            "password" => $all["password"],
            "mobile" => empty($all["mobile"]) ? null : $all["mobile"],
            "nickname" => $all["nickname"],
            "qq" => empty($all["qq"]) ? null : $all["qq"],
            "type" => 1
        ];

        $member = $this->memberRepository->createMember($params);
        $result["token"] = $this->jwtService->createToken($member["id"]);

        if (!$member) {
            return $this->return(400, "注册失败");
        }

        return $this->return(200, $result);
    }

    public function login(Request $request)
    {
        $all = $request->all();

        if (empty($all["name"])) {
            return $this->return(400, "请输入用户名或手机号码");
        }

        if (empty($all["password"])) {
            return $this->return(400, "请输入密码");
        }

        if (strlen($all["password"]) < 6 || strlen($all["password"]) > 32) {
            return $this->return(400, "密码必须6-32字符");
        }

        $result = $this->memberRepository->getMemberByNameOrPhone($all["name"]);

        if (empty($result)) {
            return $this->return(400, "用户名有误，请核对");
        }

        if (md5($all["password"]) != $result["password"]) {
            return $this->return(400, "密码有误，请核对");
        }

        unset($result["password"]);
        $result->fill(["last_login_time" => time()]);
        $result->save();
        $result["token"] = $this->jwtService->createToken($result["id"]);
        return $this->return(200, $result);
    }

    public function getMemberIndex($uid)
    {
        $member = $this->memberRepository->getMemberById($uid);
        $province = $this->regionRepository->getRegionByCode($member["province_id"]);
        $city = $this->regionRepository->getRegionByCode($member["city_id"]);
        $chatCnt = $this->chatRepository->getMemberChatCount($member["id"], 1);
        $shareCnt = $this->chatRepository->getMemberChatCount($member["id"], 2);
        $myFavoriteCnt = $this->myFavoriteRepository->getMemberMyFavoriteCount($member["id"]);
        $myFocusTopiCnt = $this->myFocusTopicRepository->getMemberMyFocusTopicCount($member["id"]);
        $myChatCommentCnt = $this->chatCommentRepository->getMemberChatCommentCount($member["id"]);
        $myPraiseCnt = $this->myPraiseRepository->getMemberMyPraiseCount($member["id"]);

        $data = [
            "mobile" => substr_replace($member["mobile"], '****', 3, 4),
            "user_name" => $member["nickname"],
            "email" => empty($member["email"]) ? "" : $member["email"],
            "topimgurl" => empty($member["topimgurl"]) ? "" : $member["topimgurl"],
            "sex" => empty($member["sex"]) ? 0 : $member["sex"],
            "mysign" => empty($member["mysign"]) ? "写个签名吧" : $member["mysign"],
            "province_id" => empty($member["province_id"]) ? "" : $member["province_id"],
            "province" => empty($province) ? "" : $province,
            "city_id" => empty($member["city_id"]) ? "" : $member["city_id"],
            "city" => empty($city) ? "" : $city,
            "focus" => empty($member["focus"]) ? 0 : $member["focus"],
            "my_chat_num" => $chatCnt,
            "my_share_num" => $shareCnt,
            "my_comment_num" => $myChatCommentCnt,
            "my_fav_num" => $myFavoriteCnt,
            "my_focus_lottery_num" => $myFocusTopiCnt,
            "my_praise_num" => $myPraiseCnt,
            "comment" => empty($member["comment"]) ? 0 : $member["comment"],
            "praise" => empty($member["praise"]) ? 0 : $member["praise"],
            "favorites" => empty($member["to_favorites"]) ? 0 : $member["to_favorites"],
        ];

        return $this->return(200, $data);
    }

    public function getMemberRelease($uid)
    {
        $data = $this->chatRepository->getMemberRelease($uid, $uid);
        return $this->return(200, $data);
    }

    public function getMemberShare($uid)
    {
        $data = $this->chatRepository->getMemberShare($uid, $uid);
        return $this->return(200, $data);
    }

    public function getMemberCollect($uid)
    {
        $memberfavData = $this->myFavoriteRepository->getMyFavoriteByUserid($uid);

        if ($memberfavData) {
            $data = $this->chatRepository->getMemberCollect($uid, $uid, $memberfavData);

            foreach ($data as $key => $item) {
                $data[$key]['user_info'] = $this->memberRepository->getChatAuther($item['user_id']);
            }
        } else {
            $data = [];
        }

        return $this->return(200, $data);
    }

    public function getMemberfoucs($uid)
    {
        $memberFocusLotteryArr = $this->myFocusTopicRepository->getMyFocusLottery($uid);
        $enableLotteryArray = $this->typeRepository->getEnableLotteryArray();
        $data = [];

        foreach ($enableLotteryArray as $lottery) {
            foreach ($memberFocusLotteryArr as $focus) {
                if ($lottery['alias'] == $focus) {
                    $data[] = $lottery;
                }
            }
        }

        return $this->return(200, $data);
    }
}
