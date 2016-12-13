<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Comment;
use App\Vote;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    //

    public function postUpVote(Request $request)
    {

        if ($request->input('thread_id') || $request->input('comment_id')) {
            if ($request->input('thread_id')){
                $hasVoted = DB::table('votes')
                    ->where('thread_id', '=', $request->input('thread_id'))
                    ->where('user_id', '=', $request->user()->id)
                    ->first();
            }


            if ($request->input('comment_id')){
                $hasVoted = DB::table('votes')
                    ->where('comment_id', '=', $request->input('comment_id'))
                    ->where('user_id', '=', $request->user()->id)
                    ->first();
            }

            if ($hasVoted) {
                $voteToChange = Vote::find($hasVoted->id);

                if($voteToChange->vote_type === 1){
                    $response = array(
                        'message' => 'Already Voted Up',
                        'status' => 400
                    );
                    return response($response, 400);
                }

                $voteToChange->vote_type = 1;
                $voteToChange->save();

                $response = array(
                    'message' => 'Success - Up Vote',
                    'status' => 200
                );
                return $response;

            }
        }

        $vote = new Vote;
        $vote->user_id = $request->user()->id;
        $vote->thread_id = $request->input('thread_id');
        $vote->comment_id = $request->input('comment_id');
        $vote->vote_type = 1;
        $vote->save();


        if ($request->input('thread_id')) {
            $response = array(
                'message' => 'Success - Up Vote',
                'status' => 200
            );

        }
        if ($request->input('comment_id')) {
            $response = array(
                'message' => 'Success - Up Vote',
                'status' => 200
            );
        }


        //$completedTasks = User::find($request->user()->id)->subs()->get();

        return response($response, 200);
    }


    public function postDownVote(Request $request)
    {

        if ($request->input('thread_id') || $request->input('comment_id')) {


            if ($request->input('thread_id')){
                $hasVoted = DB::table('votes')
                    ->where('thread_id', '=', $request->input('thread_id'))
                    ->where('user_id', '=', $request->user()->id)
                    ->first();
            }


            if ($request->input('comment_id')){
                $hasVoted = DB::table('votes')
                    ->where('comment_id', '=', $request->input('comment_id'))
                    ->where('user_id', '=', $request->user()->id)
                    ->first();
            }


            if ($hasVoted) {

                $voteToChange = Vote::find($hasVoted->id);
                if($voteToChange->vote_type === 0){
                    $response = array(
                        'message' => 'Already Voted Down',
                        'status' => 400
                    );
                    return response($response, 400);
                }

                $voteToChange->vote_type = 0;
                $voteToChange->save();

                $response = array(
                    'message' => 'Success Down',
                    'status' => 200,
                    'data' => 1
                );
                return $response;

            }
        }


        $vote = new Vote;
        $vote->user_id = $request->user()->id;
        $vote->thread_id = $request->input('thread_id');
        $vote->comment_id = $request->input('comment_id');
        $vote->vote_type = 0;
        $vote->save();


        if ($request->input('thread_id')) {
            $vote->comment_id = null;


            $response = array(
                'message' => 'Success - Down Vote',
                'status' => 200
            );

        }
        if ($request->input('comment_id')) {

            $response = array(
                'message' => 'Success - Down Vote',
                'status' => 200
            );
        }


        //$completedTasks = User::find($request->user()->id)->subs()->get();

        return response($response, 200);
    }

}
