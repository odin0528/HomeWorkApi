<?php

namespace App\Http\Controllers;

use App\Models\{Vote, VoteCandidate, VoteLog};
use App\Utils\BaseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use DB;

class VoteController extends Controller
{

    public function index(Request $request)
    {
        $all = $request->all();

        if (empty($all['page'])) {
            $page = 1;
        } else {
            $page = $all['page'];
        }

        if (empty($all['items'])) {
            $items = 20;
        } else {
            $items = $all['items'];
        }

        if (Redis::hExists('vote_list', "{$page}_{$items}")) {
            $votes = Redis::hGet('vote_list', "{$page}_{$items}");
            $votes = json_decode($votes, true);
        }else{
            $votes = Vote::query()->orderBy('id', 'DESC')->paginate($items);
            Redis::hSet('vote_list', "{$page}_{$items}", json_encode($votes));
        }

        return $this->return(200, $votes);
    }

    public function getCandidate(Request $request)
    {
        $voteId = $request->route('id');
        $all = $request->all();

        if (empty($all['page'])) {
            $page = 1;
        } else {
            $page = $all['page'];
        }

        if (empty($all['items'])) {
            $items = 20;
        } else {
            $items = $all['items'];
        }

        $votes = VoteCandidate::query()->where('vote_id', $voteId)->orderBy('id', 'ASC')->paginate($items);
        
        return $this->return(200, $votes);
    }

    public function fetchAll()
    {
        if (Redis::hExists('vote_list', "all")) {
            $votes = Redis::hGet('vote_list', "all");
            $votes = json_decode($votes, true);
        }else{
            $votes = Vote::query()->where('start_time', '<=', time())->where('end_time', '>=', time())->where('status', 1)->orderBy('id', 'DESC')->get()->toArray();
            Redis::hSet('vote_list', "all", json_encode($votes));
        }
        
        return $this->return(200, $votes);
    }

    public function fetch(Request $request){
        $id = $request->route('id');
        $vote = Vote::find($id);
        $vote['photos'] = VoteCandidate::where('vote_id', $id)->get()->toArray();
        return $this->return(200, $vote);
    }

    public function fetchCandidate(Request $request){
        $id = $request->route('id');
        $candidaties = VoteCandidate::select('id', 'title', 'photo')->where('vote_id', $id)->get()->toArray();
        return $this->return(200, $candidaties);
    }

    public function fetchLogs(Request $request){
        $id = $request->route('id');

        if (empty($all['page'])) {
            $page = 1;
        } else {
            $page = $all['page'];
        }

        if (empty($all['items'])) {
            $items = 20;
        } else {
            $items = $all['items'];
        }

        $logs = VoteLog::where('candidate_id', $id)->paginate($items);
        return $this->return(200, $logs);
    }

    public function save(Request $request){
        $all = $request->all();

        if($all['id'] > 0){
            $vote = Vote::find($all['id']);
        }else{
            $vote = new Vote;
        }

        $vote->title = $all['title'];
        $vote->description = $all['description'];
        $vote->start_time = strtotime($all['start_time']);
        $vote->end_time = strtotime($all['end_time']);
        $vote->created_by = $all['adminId'];
        $vote->updated_by = $all['adminId'];
        $vote->save();

        foreach($all['photos'] as $photo){
            if($photo['id'] !== 0){
                $candidate = VoteCandidate::find($photo['id']);
            }else{
                $candidate = new VoteCandidate;
                $candidate->vote_id = $vote->id;
            }
            
            $candidate->title = $photo['title'];
            
            $img = BaseUtil::base64Analysis($photo['photo']);
            if($img['image_string']){
                $imgData = BaseUtil::base64Decode($img['image_string']);
                $randname = time() . rand(0, 3276);
                $imgurl = 'upload/' . $randname . '.' . $img['ext'];
                Storage::disk('public')->put($imgurl, $imgData);
                $candidate->photo = $imgurl;
            }
            $candidate->save();
        }

        Redis::del('vote_list');
        
        return $this->return(200, $vote);
    }

    public function vote(Request $request){
        $all = $request->all();
        $vote = VoteCandidate::find($all['candidateId']);

        if(empty($vote))
            return $this->return(400);

        try{
            $log = new VoteLog;
            $log->vote_id = $vote['vote_id'];
            $log->candidate_id = $all['candidateId'];
            $log->email = $all['email'];
            $log->id = $all['id'];
            $log->save();
        }catch(\Exception $e){
            return $this->return(9001);
        }

        $candidaties = VoteCandidate::select('id', 'title', 'photo', 'vote')->where('vote_id', $vote['vote_id'])->get()->toArray();
        return $this->return(200, $candidaties);
    }

    public function updateStatus(Request $request){
        
        $all = $request->all();
        $vote = Vote::find($all['id']);

        if(empty($vote))
            return $this->return(400);

        $vote->status = $all['status'];
        $vote->save();

        if($all['status'] === 2){
            mail(
                'odin9130@gmail.com',
                'simple vote system',
                'test'
            );
        }

        return $this->return(200);
    }
}
