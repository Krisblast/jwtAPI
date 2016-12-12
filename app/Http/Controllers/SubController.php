<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Sub;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class SubController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($id = null)
    {

        if ($id == null) {
            $subs = Sub::orderBy('id', 'asc')
                ->join('users', 'subs.user_id', '=', 'users.id')
                ->leftJoin('threads', 'threads.sub_id', '=', 'subs.id')
                ->select(DB::raw('subs.*, users.name as user_name, COUNT(threads.id) AS threads_count'))
                ->groupBy('subs.id')
                ->get();

            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $subs
            );
            return response($response, 200);
        } else {

            $sub = Sub::where('sub_name', $id)->first();

            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $sub
            );
            return response($response, 200);
        }
    }


    public function store(Request $request)
    {
        $rules = array(
            'sub_name' => 'required|unique:subs',
            'description' => 'required',
        );


        $validator = Validator::make(Input::all(), $rules);
        $response = array(
            'message' => 'Failed',
            'status' => 400,
            'errors' => $validator->errors()
        );
        if ($validator->fails()) {
            return response($response, 400);
        } else {
            $sub = new Sub;
            $sub->sub_name = $request->input('sub_name');
            $sub->description = $request->input('description');
            $sub->user_id = $request->user()->id;
            $sub->save();
            //$completedTasks = User::find($request->user()->id)->subs()->get();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $sub
            );
            return response($response, 200);
        }
    }


    public function getAllSubscribed(Request $request)
    {

        if ($request->input('page') == null) {
            $page = 1;

        } else {
            $page = $request->input('page');
        }


        $subscription_threads =
            User::find($request->user()->id)
                ->subscriptions()
                ->select(DB::raw('threads.*,  subs.sub_name, COUNT(comments.id) AS comment_count'))
                ->join('threads', 'threads.sub_id', '=', 'subscribers.sub_id')
                ->leftJoin('subs', 'subscribers.sub_id', '=', 'subs.id')
                ->leftJoin('comments', 'threads.id', '=', 'comments.thread_id')
                ->groupBy('threads.id')
                ->orderBy('threads.created_at', 'desc')
                ->paginate(25);


        foreach ($subscription_threads as $thread){
            $this->hasUserVotedOnThread($request,$thread);
        }


        $response = array(
            'message' => 'Success',
            'status' => 200,
            'data' => $subscription_threads,
            'page' => $page
        );
        return response($response, 200);
    }

}