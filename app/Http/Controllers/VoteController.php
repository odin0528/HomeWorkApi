<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Utils\BaseUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

        $votes = Vote::query()
            ->offset(($page - 1) * $items)
            ->limit($items)
            ->get()
            ->toArray();
        
        return $this->return(200, $votes);
    }
}
