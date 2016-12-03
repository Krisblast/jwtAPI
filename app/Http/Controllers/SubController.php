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

        $subs = Sub::orderBy('id', 'asc')->get();

        foreach ($subs as $sub) {
            $sub->user_name = DB::table('users')->where('id', '=', $sub->user_id)->first()->name;
        }

        if ($id == null) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $subs
            );
            return response($response, 200);
        } else {
            $sub = Sub::find($id);
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


        $subscriptions = User::find($request->user()->id)->subscriptions()
            ->join('subs', 'subscribers.sub_id', '=', 'subs.id')
            ->join('users', 'subscribers.user_id', '=', 'users.id')
            ->get();



        //FIXME Is this really the way to do it?
        $collection = collect([]);

        foreach ($subscriptions as $user_subscription) {


            //Find 30 threads with most votes form each sub the user is subscribed to.

            $threads = DB::table('threads')
                ->groupBy('id')
                ->orderBy('created_at', 'desc')
                ->orderBy('total_votes', 'desc')
                ->where('sub_id', '=', $user_subscription->sub_id)

                //->where('total_votes', '!=', 0)
                ->limit(30) //How many threads we take from each sub
                ->get();

            if ($threads->count()) {
                foreach ($threads as $threadToArray) {
                    $threadToArray->sub_name = $user_subscription->sub_name;
                    $collection->push($threadToArray);
                }
            }
        }

            if ($request->input('page') == null) {
                $page = 1;

            } else {
                $page = $request->input('page');
            }



        $collection = $collection->sortBy('total_votes', null, true)->forPage($page, 30); // Page, PageSize

        $newArray = [];

        foreach ($collection as $threadToArray) {
            $commentCount = DB::table('comments')->where('thread_id', '=', $threadToArray->id)->count();
            $threadToArray->comment_count = $commentCount;

            if ($request->user()) {
                $hasVoted = DB::table('votes')
                    ->where('thread_id', '=', $threadToArray->id)
                    ->where('user_id', '=', $request->user()->id)
                    ->first();

                if ($hasVoted) {
                    $threadToArray->has_voted = true;
                    $threadToArray->vote_type = $hasVoted->vote_type;
                } else {
                    $threadToArray->has_voted = false;
                }
            }
            $newArray = array_prepend($newArray, $threadToArray);
        }


        $response = array(
            'message' => 'Success',
            'status' => 200,
            'data' => array_reverse($newArray),
            'page' => $page
        );
        return response($response, 200);
    }

}