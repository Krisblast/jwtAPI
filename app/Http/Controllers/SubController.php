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
            $subs = Sub::orderBy('id', 'asc')->get();

            foreach ($subs as $sub) {
                $sub->user_name = DB::table('users')->where('id', '=', $sub->user_id)->first()->name;
            }

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

        $subscriptions = User::find($request->user()->id)->subscriptions()
            ->join('subs', 'subscribers.sub_id', '=', 'subs.id')
            ->join('users', 'subscribers.user_id', '=', 'users.id')
            ->get();

        //FIXME Is this really the way to do it?
        $collection = collect([]);

        //Get threads for every sub the user is subscribed to
        foreach ($subscriptions as $user_subscription) {

            //Find 30 threads with most votes from each sub the user is subscribed to.
            $threads = DB::table('threads')
                ->groupBy('id')
                ->orderBy('created_at', 'desc')
                ->orderBy('total_votes', 'desc')
                ->where('sub_id', '=', $user_subscription->sub_id) //Fixme maybe we could make a query where we get all the threads we need
                //->where('total_votes', '!=', 0)
                ->limit(30)//How many threads we take from each sub
                ->get();


            //If we have threads add the threads to the collection
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
            $threadToArray->comment_count = $this->getThreadCommentCount($threadToArray->id);

            $this->hasUserVotedOnThread($request,$threadToArray);

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