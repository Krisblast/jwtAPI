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

        if ($request->input('thread_id')) {
            $hasVoted = DB::table('votes')
                ->where('thread_id', '=', $request->input('thread_id'))
                ->where('user_id', '=', $request->user()->id)
                ->first();
            if ($hasVoted) {

                $voteToChange = Vote::find($hasVoted->id);

                if($voteToChange->vote_type == 1){
                    $message = 'the last vote type was 1 we should not do anything';
                }
                if($voteToChange->vote_type == 0){

                    $thread = Thread::find($voteToChange->thread_id);
                    if($thread->down_votes >= 1){
                        $thread->down_votes -= 1;
                        $thread->up_votes += 1;
                        $thread->total_votes = $thread->up_votes - $thread->down_votes;

                    }
                    $message = 'the last vote type was 0 we should remove one from total down votes and +1 to total up votes on thread';
                    $thread->save();

                }

                $voteToChange->vote_type = 1;
                $voteToChange->save();

                $response = array(
                    'message' => $message,
                    'status' => 200,
                    'data' => $thread->total_votes
                );
                return $response;
            }
        }
        if ($request->input('comment_id')) {

            $hasVoted = DB::table('votes')
                ->where('comment_id', '=', $request->input('comment_id'))
                ->where('user_id', '=', $request->user()->id)
                ->first();

            if ($hasVoted) {

                $voteToChange = Vote::find($hasVoted->id);

                if($voteToChange->vote_type == 1){
                    $message = 'the last vote type was 1 we should not do anything';
                }
                if($voteToChange->vote_type == 0){
                    $comment = Comment::find($voteToChange->comment_id);
                    if($comment->down_votes >= 1){
                        $comment->down_votes -= 1;
                        $comment->up_votes += 1;
                        $comment->total_votes = $comment->up_votes - $comment->down_votes;

                    }
                    $message = 'the last vote type was 0 we should remove one from total down votes and +1 to total up votes on comment';
                    $comment->save();
                }

                $voteToChange->vote_type = 1;
                $voteToChange->save();

                $threadUpVotes = DB::table('votes')
                    ->where('comment_id', '=', $request->input('comment_id'))
                    ->where('vote_type', '=', 1)
                    ->count();


                $threadDownVotes = DB::table('votes')
                    ->where('comment_id', '=', $request->input('comment_id'))
                    ->where('vote_type', '=', 0)
                    ->count();

                $total_votes = $threadUpVotes - $threadDownVotes;

                $response = array(
                    'message' => $message,
                    'status' => 200,
                    'data' => $total_votes
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


            $thread = Thread::find($request->input('thread_id'));
            $thread->up_votes += 1;
            $thread->total_votes = $thread->up_votes - $thread->down_votes;
            $thread->save();

            $response = array(
                'message' => 'Success - Up Vote',
                'status' => 200,
                'data' => $thread->total_votes
            );

        }
        if ($request->input('comment_id')) {

            $comment = Comment::find($request->input('comment_id'));
            $comment->up_votes += 1;
            $comment->total_votes = $comment->up_votes - $comment->down_votes;
            $comment->save();

            $response = array(
                'message' => 'Success - Up Vote',
                'status' => 200,
                'data' => $comment->total_votes
            );
        }


        return response($response, 200);
    }


    public function postDownVote(Request $request)
    {

        if ($request->input('thread_id')) {
            $hasVoted = DB::table('votes')
                ->where('thread_id', '=', $request->input('thread_id'))
                ->where('user_id', '=', $request->user()->id)
                ->first();
            if ($hasVoted) {

                $voteToChange = Vote::find($hasVoted->id);

                if($voteToChange->vote_type == 1){
                    $message = 'the last vote type was 1 we should remove one from total up votes and +1 to total down votes on thread';
                    $thread = Thread::find($voteToChange->thread_id);

                    if($thread->up_votes >= 1){
                        $thread->down_votes += 1;
                        $thread->up_votes -= 1;
                        $thread->total_votes = $thread->up_votes - $thread->down_votes;

                    }
                    $thread->save();
                }
                if($voteToChange->vote_type == 0){
                    $message = 'the last vote type was 0 we should not do anything';

                }
                $voteToChange->vote_type = 0;
                $voteToChange->save();

                $response = array(
                    'message' => $message,
                    'status' => 200,
                    'data' => $thread->total_votes
                );
                return $response;

            }
        }

        if ($request->input('comment_id')) {

            $hasVoted = DB::table('votes')
                ->where('comment_id', '=', $request->input('comment_id'))
                ->where('user_id', '=', $request->user()->id)
                ->first();

            if ($hasVoted) {

                $voteToChange = Vote::find($hasVoted->id);

                if($voteToChange->vote_type == 0){
                    $message = 'the last vote type was 0 we should not do anything';
                }
                if($voteToChange->vote_type == 1){
                    $comment = Comment::find($voteToChange->comment_id);


                    if($comment->up_votes >= 1){
                        $comment->down_votes += 1;
                        $comment->up_votes -= 1;
                        $comment->total_votes = $comment->up_votes - $comment->down_votes;
                    }
                    $message = 'the last vote type was 0 we should remove one from total down votes and +1 to total up votes on comment';
                    $comment->save();
                }
                $voteToChange->vote_type = 0;
                $voteToChange->save();

                $response = array(
                    'message' => $message,
                    'status' => 200,
                    'data' => $comment->total_votes
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



            $thread = Thread::find($request->input('thread_id'));
            $thread->down_votes += 1;
            $thread->total_votes = $thread->up_votes - $thread->down_votes;
            $thread->save();
            $response = array(
                'message' => 'Success - Down Vote',
                'status' => 200,
                'data' => $thread->total_votes
            );

        }
        if ($request->input('comment_id')) {
            $vote->thread_id = null;

            $comment = Comment::find($request->input('comment_id'));
            $comment->down_votes += 1;
            $comment->total_votes = $comment->up_votes - $comment->down_votes;
            $comment->save();
            $response = array(
                'message' => 'Success - Down Vote',
                'status' => 200,
                'data' => $comment->total_votes
            );
        }


        //$completedTasks = User::find($request->user()->id)->subs()->get();

        return response($response, 200);
    }

}
