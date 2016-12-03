<?php

namespace App\Http\Controllers;

use App\User;
use App\Sub;
use App\Thread;
use App\Comment;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class CommentController extends Controller
{
    public function index($type, $id, Request $request)
    {

        if($type === 'user'){
            $comments = User::find($id)->comments()->get();
            //TODO Check if user exists
        }
        else if($type === 'sub'){
            $comments = Sub::find($id)->comments()->get();
            //TODO Check if user exists
        }
        else if($type === 'thread'){
            $comments = Thread::find($id)->comments()->get();
            //TODO Check if user exists


            if($request->user()){
                foreach ($comments as $comment){

                    $hasVoted = DB::table('votes')
                        ->where('comment_id', $comment->id)
                        ->where('user_id', '=', $request->user()->id)
                        ->first();


                    if ($hasVoted){
                        $comment->has_voted = true;
                        $comment->vote_type = $hasVoted->vote_type;
                    }
                    else {
                        $comment->has_voted = false;
                    }

                }

            }
        }




        else {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => 'Not a valid search type'
            );
            return response($response, 400);
        }

        $response = array(
            'message' => 'Success',
            'status' => 200,
            'data' => $comments
        );
        return response($response, 200);

    }



    public function store(Request $request)
    {
        $rules = array(
            'comment_text' => 'required',
            'sub_id' => 'required',
            'thread_id' => 'required',
        );
        $validator = Validator::make(Input::all(),$rules);

        if($validator->fails()){
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => $validator->errors()
            );
            return response($response, 400);
        }

        else {
            $comment = new Comment();
            $comment->sub_id = $request->input('sub_id');
            $comment->thread_id = $request->input('thread_id');
            $comment->user_id = $request->user()->id;
            $comment->user_name = $request->user()->name;
            $comment->comment_text = $request->input('comment_text');
            $comment->comment_ref_id = $request->input('comment_ref_id');
            $comment->up_votes = 0;
            $comment->down_votes = 0;
            $comment->total_votes = 0;
            $comment->save();
            //$completedTasks = User::find($request->user()->id)->subs()->get();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $comment
            );
            return response($response, 200);
        }
    }
}
