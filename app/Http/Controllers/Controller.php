<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use App\User;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function getThreadCommentCount($id){
        $commentCount = DB::table('comments')->where('thread_id', '=', $id)->count();
        return $commentCount;
    }


    public function hasUserVotedOnThread($request, $thread){
        if($request->user()){

            $hasVoted = User::find($request->user()->id)->votes()->where('thread_id', '=', $thread->id)->first();

            if ($hasVoted){
                $thread->has_voted = true;
                $thread->vote_type = $hasVoted->vote_type;
            }
            else {
                $thread->has_voted = false;
            }
        }
    }

}
