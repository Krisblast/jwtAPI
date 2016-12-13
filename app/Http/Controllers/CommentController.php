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


        if ($request->user()){
            if ($type === 'user') {
                $comments = User::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND comments.id = votes.comment_id) as vote_type,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();


                //TODO Check if user exists
            } else if ($type === 'sub') {
                $comments = Sub::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    subs.sub_name, 
                    (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND comments.id = votes.comment_id) as vote_type,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();
                //TODO Check if user exists
            } else if ($type === 'thread') {
                $comments = Thread::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND comments.id = votes.comment_id) as vote_type,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();
                //TODO Check if user exists


            } else {
                $response = array(
                    'message' => 'Failed',
                    'status' => 400,
                    'errors' => 'Not a valid search type'
                );
                return response($response, 400);
            }
        }
        else {
            if ($type === 'user') {
                $comments = User::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();


                //TODO Check if user exists
            } else if ($type === 'sub') {
                $comments = Sub::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    subs.sub_name, 
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();
                //TODO Check if user exists
            } else if ($type === 'thread') {
                $comments = Thread::find($id)->comments()
                    ->select(DB::raw(
                        'comments.*,  
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where comments.id = votes.comment_id AND votes.vote_type = 0) as down_votes'))
                    ->get();
                //TODO Check if user exists


            } else {
                $response = array(
                    'message' => 'Failed',
                    'status' => 400,
                    'errors' => 'Not a valid search type'
                );
                return response($response, 400);
            }
        }

        foreach ($comments as $comment) {
            $comment->total_votes = $comment->up_votes - $comment->down_votes;
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
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => $validator->errors()
            );
            return response($response, 400);
        } else {
            $comment = new Comment();
            $comment->sub_id = $request->input('sub_id');
            $comment->thread_id = $request->input('thread_id');
            $comment->user_id = $request->user()->id;
            $comment->user_name = $request->user()->name;
            $comment->comment_text = $request->input('comment_text');
            $comment->comment_ref_id = $request->input('comment_ref_id');
            $comment->save();
            $comment->total_votes = 0;
            $comment->vote_type = null;

            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $comment
            );
            return response($response, 200);
        }
    }
}
