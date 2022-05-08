<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{AdminController, VoteController};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'admin'
], function () {
    Route::post('login', 'AdminController@login');

    Route::middleware('verifyJWT')->group(function () {
        Route::post('votes', [VoteController::class, 'index']);
    });
});





Route::group([
    'prefix' => 'lottery'
], function () {
    Route::get('type', 'LotteryTypeController@getAll');
    Route::get('type/{lotteryAlias}', 'LotteryTypeController@get');
    Route::get('result/{lotteryAlias}', 'LotteryController@getResult');
    Route::get('info/{lotteryAlias}', 'LotteryController@getInfo');
    Route::get('history/{lotteryAlias}', 'LotteryController@getHistory');

    Route::get('historySummary/{lotteryAlias}', 'LotteryController@getHistorySummary'); // 雙面統計
    Route::get('continueSummary/{lotteryAlias}', 'LotteryController@getContinueSummary'); // 長龍統計 ?
    Route::get('hotColdSummary/{lotteryAlias}', 'LotteryController@getHotColdSummary'); // 冷热分析
    Route::get('numberSummary/{lotteryAlias}', 'LotteryController@getNumberSummary'); // 今日号码统计

    Route::get('twoSideHistory/{lotteryAlias}', 'LotteryController@getTwoSideHistory'); // 雙面統計
    Route::get('numberHistory/{lotteryAlias}', 'LotteryController@getNumberHistory'); // 號碼統計
    Route::get('/compareHistory/{lotteryAlias}', 'LotteryController@getCompareHistory'); // 龙虎統計
    Route::get('sumHistory/{lotteryAlias}', 'LotteryController@getSumHistory'); // 冠亚和历史 ?
    Route::get('/continueHistory/{lotteryAlias}', 'LotteryController@getContinueHistory'); // 每日长龙统计 ?

    Route::get('twoSideReport/{lotteryAlias}', 'LotteryController@getTwoSideReport'); // 雙面路珠
    Route::get('numberReport/{lotteryAlias}', 'LotteryController@getNumberReport'); // 號碼路珠
    Route::get('compareReport/{lotteryAlias}', 'LotteryController@getCompareReport'); // 龍虎路珠
    Route::get('totalReport/{lotteryAlias}', 'LotteryController@getTotalReport'); // 總和路珠
    Route::get('sumReport/{lotteryAlias}', 'LotteryController@getSumReport'); // 冠亞和路珠 ?
    Route::get('sectionReport/{lotteryAlias}', 'LotteryController@getSectionReport'); // 号码前后路珠
    Route::get('ternaryReport/{lotteryAlias}', 'LotteryController@getTernaryReport'); // 中發白路珠
    Route::get('sumTwoSideReport/{lotteryAlias}', 'LotteryController@getSumTwoSideReport'); // 合數單雙路珠
    Route::get('lastTwoSideReport/{lotteryAlias}', 'LotteryController@getLastTwoSideReport'); // 尾數大小路珠
    Route::get('directionReport/{lotteryAlias}', 'LotteryController@getDirectionReport'); // 东南西北路珠 ?
    Route::get('report/{lotteryAlias}', 'LotteryController@getReport'); // 綜合路珠
});

Route::group([
    'prefix' => 'chart'
], function () {
    Route::get('basic', 'ChartController@basic'); // 基本走勢 ?
    Route::get('compare', 'ChartController@compare'); // 龍虎走勢 ?
    Route::get('shape', 'ChartController@shape'); // 形態走勢 ?
    Route::get('number', 'ChartController@number'); // 號碼分布走勢 ?
    Route::get('mod3', 'ChartController@mod3'); // 012路走勢 ?
    Route::get('rise', 'ChartController@rise'); // 升平降走勢 ?
    Route::get('feature', 'ChartController@feature'); // 形態特徵走勢 ?
    Route::get('sum', 'ChartController@sum'); // pk10冠亞和走勢、11選5和值號碼分佈 ?
    Route::get('big', 'ChartController@big'); // 大小走勢 ?
    Route::get('bigRate', 'ChartController@bigRate'); // 大小比走勢 ?
    Route::get('odd', 'ChartController@odd'); // 單雙走勢 ?
    Route::get('oddRate', 'ChartController@oddRate'); // 單雙比走勢 ?
});

Route::post('register', 'MemberController@register');
Route::post('login', 'MemberController@login');

Route::group([
    'prefix' => 'member'
], function () {
    Route::get('index/{uid}', 'MemberController@getMemberIndex');
    Route::get('release/{uid}', 'MemberController@getMemberRelease');
    Route::get('share/{uid}', 'MemberController@getMemberShare');
    Route::get('collect/{uid}', 'MemberController@getMemberCollect');
    Route::get('foucs/{uid}', 'MemberController@getMemberfoucs');
});

Route::group([
    'prefix' => 'my'
], function () {
    Route::get('index', 'MyController@getMyIndex');
    Route::get('release', 'MyController@getMyRelease');
    Route::get('commentTopic', 'MyController@getMyCommentTopic');
    Route::get('share', 'MyController@getMyShare');
    Route::get('collect', 'MyController@getMyCollect');
    Route::get('praise', 'MyController@getMyPraise');
    Route::get('foucs', 'MyController@getMyfoucs');
    Route::post('modifyInfo', 'MyController@modifyInfo');
    Route::post('changePassword', 'MyController@changePassword');
    Route::post('feedback', 'MyController@feedback');
});

Route::group([
    'prefix' => 'chat'
], function () {
    Route::get('index', 'ChatController@getChatIndex');
    Route::get('chat/{id}', 'ChatController@getChat');
    Route::post('favorites', 'ChatController@favorites');
    Route::post('cancelFavorites', 'ChatController@cancelFavorites');
    Route::post('praise', 'ChatController@praise');
    Route::post('sendComment', 'ChatController@sendComment');
    Route::post('release', 'ChatController@release');
    Route::get('topic', 'ChatController@getTopicIndex');
});

Route::group([
    'prefix' => 'topic'
], function () {
    Route::get('topics', 'TopicController@getTopicList');
    Route::post('focus', 'TopicController@focusTopic');
    Route::post('cancelFocus', 'TopicController@cancelFocusTopic');
    Route::get('focus', 'TopicController@getMyFocusLottery');
});