<?php

namespace App\Http\Controllers;

use App\Repository\ChatCommentRepository;
use App\Repository\ChatRepository;
use App\Repository\FeedbackRepository;
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
use Illuminate\Support\Facades\Storage;

class MyController extends Controller
{
    public function __construct(
        ChatCommentRepository $chatCommentRepository,
        ChatRepository $chatRepository,
        FeedbackRepository $feedbackRepository,
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
        $this->feedbackRepository = $feedbackRepository;
        $this->jwtService = $jwtService;
    }

    public function getMyIndex(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $member = $this->memberRepository->getMemberById($userid);
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

    public function getMyRelease(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $data = $this->chatRepository->getMemberRelease($userid, $userid);
        return $this->return(200, $data);
    }

    public function getMyCommentTopic(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $data = $this->chatRepository->getMyCommentTopic($userid);
        return $this->return(200, $data);
    }

    public function getMyShare(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $data = $this->chatRepository->getMemberShare($userid, $userid);
        return $this->return(200, $data);
    }

    public function getMyCollect(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $myfavData = $this->myFavoriteRepository->getMyFavoriteByUserid($userid);

        if ($myfavData) {
            $data = $this->chatRepository->getMemberCollect($userid, $userid, $myfavData);

            foreach ($data as $key => $item) {
                $data[$key]['user_info'] = $this->memberRepository->getChatAuther($item['user_id']);
            }
        } else {
            $data = [];
        }

        return $this->return(200, $data);
    }

    public function getMyPraise(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $myPraise = $this->myPraiseRepository->getChatIdByUserId($userid);

        if ($myPraise) {
            $data = $this->chatRepository->getMemberPraise($myPraise);

            foreach ($data as $key => $item) {
                $data[$key]['user_info'] = $this->memberRepository->getChatAuther($item['user_id']);
            }
        } else {
            $data = null;
        }

        return $this->return(200, $data);
    }

    public function getMyfoucs(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $myFocusLotteryArr = $this->myFocusTopicRepository->getMyFocusLottery($userid);
        $enableLotteryArray = $this->typeRepository->getEnableLotteryArray();
        $data = [];

        foreach ($enableLotteryArray as $lottery) {
            foreach ($myFocusLotteryArr as $focus) {
                if ($lottery['alias'] == $focus) {
                    $data[] = $lottery;
                }
            }
        }

        return $this->return(200, $data);
    }

    public function modifyInfo(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $params = [];
        $all = $request->all();

        if (empty($all['nickname'])) {
            return $this->return(400, "请输入暱称");
        }

        $params['nickname'] = $all['nickname'];

        if (!in_array($all['sex'], [0, 1, 2])) {
            return $this->return(400, "性别错误");
        }

        $params['sex'] = $all['sex'];

        if (!empty($all['sign'])) {
            $params['mysign'] = $all['sign'];
        }

        if (!empty($all['image'])) {
            $img = BaseUtil::base64Analysis($all['image']);
            $check = BaseUtil::checkFileSize2M($img['image_string']);

            if ($check) {
                return $this->return(400, "图片须小于2M");
            }

            $imgData = BaseUtil::base64Decode($img['image_string']);
            $topimgurl = 'upload/' . $userid . '-head-' . time() . '.' . $img['ext'];
            Storage::disk('public')->put($topimgurl, $imgData);
            $params['topimgurl'] = $topimgurl;
        }

        $this->memberRepository->updateMember($params, $userid);
        return $this->return(200, "个人资料修改成功");
    }

    public function changePassword(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $all = $request->all();

        if (empty($all['oldPassword']) || empty($all['newPassword']) || empty($all['confirmPassword'])) {
            return $this->return(400, "请输入所有参数");
        }

        if ($all['newPassword'] != $all['confirmPassword']) {
            return $this->return(400, "二次输入密码不同");
        }

        $member = $this->memberRepository->getMemberById($userid);

        if ($member['password'] != md5($all['oldPassword'])) {
            return $this->return(400, "旧密码错误");
        }

        $this->memberRepository->changePassword(['password' => md5($all['newPassword'])], $userid);
        return $this->return(200, "密码修改成功");
    }

    public function feedback(Request $request)
    {
        $userid = $this->jwtService->verifyToken($request);

        if (!$userid) {
            return $this->return(401, "请登陆");
        }

        $all = $request->all();

        if (empty($all['content']) || empty($all['tel'])) {
            return $this->return(400, "请输入产品意见与联系方式");
        }

        $params = [
            'uid'     => $userid,
            'content' => $all['content'],
            'tel'     => $all['tel'],
        ];

        $this->feedbackRepository->createFeedback($params);
        return $this->return(200, "意见反馈提交成功");
    }
}
