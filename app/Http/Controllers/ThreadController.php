<?php

namespace App\Http\Controllers;
use App\Sub;
use App\User;
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


        if($request->user()){
            if($id === null){
                //TODO How should we define a thread as "hot"
                $threads = Thread::orderBy('created_at', 'desc')
                    ->leftJoin('subs', 'threads.sub_id', '=', 'subs.id')
                    ->leftJoin('comments', 'threads.id', '=', 'comments.thread_id')
                    ->select(DB::raw(
                        'threads.*,  
                    subs.sub_name, 
                    COUNT(comments.id) AS comment_count, 
                    (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND threads.id = votes.thread_id) as vote_type,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                    ->groupBy('threads.id')
                    ->paginate(25);
            }
            else {
                //TODO How should we define a thread as "hot"
                $threads = Sub::find($id)->threads()
                    ->leftJoin('subs', 'threads.sub_id', '=', 'subs.id')
                    ->leftJoin('comments', 'threads.id', '=', 'comments.thread_id')
                    ->select(DB::raw(
                        'threads.*,  
                    subs.sub_name, 
                    COUNT(comments.id) AS comment_count, 
                    (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND threads.id = votes.thread_id) as vote_type,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                    ->groupBy('threads.id')
                    ->paginate(25);
            }
        }
        else {
            if($id === null){
                //TODO How should we define a thread as "hot"
                $threads = Thread::orderBy('created_at', 'desc')
                    ->leftJoin('subs', 'threads.sub_id', '=', 'subs.id')
                    ->leftJoin('comments', 'threads.id', '=', 'comments.thread_id')
                    ->select(DB::raw(
                        'threads.*,  
                    subs.sub_name, 
                    COUNT(comments.id) AS comment_count, 
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                    ->groupBy('threads.id')
                    ->paginate(25);
            }
            else {
                //TODO How should we define a thread as "hot"
                $threads = Sub::find($id)->threads()
                    ->leftJoin('subs', 'threads.sub_id', '=', 'subs.id')
                    ->leftJoin('comments', 'threads.id', '=', 'comments.thread_id')
                    ->select(DB::raw(
                        'threads.*,  
                    subs.sub_name, 
                    COUNT(comments.id) AS comment_count, 
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                    (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                    ->groupBy('threads.id')
                    ->paginate(25);
            }
        }


        foreach ($threads as $thread){
            $thread->total_votes = $thread->up_votes - $thread->down_votes;
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
        //TODO Maybe get sub name in here? or should the user just make a call to get the sub info.
        if ($request->user()){
            $thread = Thread::orderBy('id', 'desc')
                ->where('id', $thread_id)
                ->select(DB::raw(
                    'threads.*,
                 (select votes.vote_type FROM votes where ' . $request->user()->id . ' = votes.user_id AND threads.id = votes.thread_id) as vote_type,
                 (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                 (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                ->first();
        }
        else {
            $thread = Thread::orderBy('id', 'desc')
                ->where('id', $thread_id)
                ->select(DB::raw(
                    'threads.*,
                 (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 1) as up_votes,
                 (select COUNT(*) FROM votes where threads.id = votes.thread_id AND votes.vote_type = 0) as down_votes'))
                ->first();
        }

        if ($thread) {
            $thread->total_votes = $thread->up_votes - $thread->down_votes;
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

            $thread = new Thread();
            $thread->sub_id = $request->input('sub_id');
            $thread->user_id = $request->user()->id;
            $thread->user_name = $request->user()->name;
            $thread->title = $request->input('title');
            $thread->image = $request->input('image');
            $thread->text = $request->input('text');
            $thread->link = $request->input('link');
            $thread->save();
            $thread->comment_count = 0;
            $thread->up_votes = 0;
            $thread->down_votes = 0;
            $thread->total_votes = 0;
            $thread->vote_type = null;

            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $thread
            );
            return response($response, 200);
        }
    }



}
