<?php

namespace App\Http\Controllers;
use App\Sub;
use App\User;
use App\Vote;
use App\Thread;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Pagination\PaginationServiceProvider;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;


class ThreadController extends Controller
{
    public function index( Request $request, $id = null)
    {

        if($id === null){
            if($request->input('order') === "hot"){
                //TODO How should we define a thread as "hot"
                $threads = Thread::orderBy('total_votes', 'desc')
                    ->paginate(25);

                // updated_at today
                // above avg total votes?
                //if created within a week
                //if updated_at recently?
            }

            if($request->input('order') === "new"){
                $threads = Thread::orderBy('created_at', 'desc')
                    ->paginate(25);
            }
            if($request->input('order') === "top"){
                //TODO How should we define a thread as "hot"
                $threads = Thread::orderBy('total_votes', 'desc')
                    ->paginate(25);
            }
        }

        else {
            if($request->input('order') === "hot"){
                //TODO How should we define a thread as "hot"
                $threads = Sub::find($id)->threads()
                    ->orderBy('updated_at', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->orderBy('total_votes', 'desc')
                    ->paginate(25);

                // updated_at today
                // above avg total votes?
                //if created within a week
                //if updated_at recently?
            }
            if($request->input('order') === "new"){
                $threads = Sub::find($id)->threads()
                    ->orderBy('created_at', 'desc')
                    ->paginate(25);
            }
            if($request->input('order') === "top"){
                $threads = Sub::find($id)->threads()
                    ->orderBy('total_votes', 'desc')
                    ->paginate(25);
            }
        }


        foreach ($threads as $thread) {
            $commentCount = DB::table('comments')->where('thread_id', '=', $thread->id)->count();
            $thread->comment_count = $commentCount;

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
        if ($threads) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $threads
            );
            return response($response, 200);
        }
        else {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => 'No Threads'
            );
            return response($response, 400);
        }
    }


    public function getThreadDetail($thread_id, Request $request)
    {
        $thread = DB::table('threads')
            ->where('id', '=', $thread_id)
            ->first();

        $thread->sub_name = DB::table('subs')
            ->where('id', '=', $thread->sub_id)
            ->first()->sub_name;



        if($request->user()){
            $hasVoted = DB::table('votes')
                ->where('thread_id', '=', $thread->id)
                ->where('user_id', '=', $request->user()->id)
                ->first();


            if ($hasVoted){
                $thread->has_voted = true;
                $thread->vote_type = $hasVoted->vote_type;
            }
            else {
                $thread->has_voted = false;
            }
        }

        if ($thread) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $thread
            );
            return response($response, 200);
        }
        else {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => 'No Thread'
            );
            return response($response, 400);
        }
    }


    public function store(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'sub_id' => 'required',
            'link' => 'url',
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


//            $file = $request->image;
//            $imagedata = file_get_contents($file);
//            $base64 = base64_encode($imagedata);

            $thread = new Thread();
            $thread->sub_id = $request->input('sub_id');
            $thread->user_id = $request->user()->id;
            $thread->user_name = $request->user()->name;
            $thread->title = $request->input('title');
            $thread->image = $request->input('image');
            $thread->text = $request->input('text');
            $thread->link = $request->input('link');
            $thread->up_votes = 0;
            $thread->down_votes = 0;
            $thread->total_votes = 0;
            $thread->save();
            $thread->comment_count = 0;
            //$completedTasks = User::find($request->user()->id)->subs()->get();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $thread
            );
            return response($response, 200);
        }
    }



}
