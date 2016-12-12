<?php

namespace App\Http\Controllers;

use App\User;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

class SubscriberController extends Controller
{
    public function store(Request $request)
    {
        $rules = array(
            'sub_id' => 'required'
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

            $userSubs = User::find($request->user()->id)->subscriptions()->get();

            //TODO How to do the smarter - collection contains()???
            foreach ($userSubs as $userSubData) {
                if($userSubData->sub_id == $request->input('sub_id')){
                    $response = array(
                        'message' => 'Failed',
                        'status' => 400,
                        'errors' => 'Already Subscribed'
                    );
                    return response($response, 400);
                }
            }
            $subscriber = new Subscriber();
            $subscriber->sub_id = $request->input('sub_id');
            $subscriber->user_id = $request->user()->id;
            $subscriber->save();
            //$completedTasks = User::find($request->user()->id)->subs()->get();
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $userSubs
            );
            return response($response, 200);
        }
    }



    public function index(Request $request)
    {
        $subs = User::find($request->user()->id)->subscriptions()->join('subs', 'subscribers.sub_id', '=', 'subs.id')->get();


        if ($subs) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $subs
            );
            return response($response, 200);
        }
        else {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => 'No subscriptions'
            );
            return response($response, 400);
        }
    }


    public function destroy($id, Request $request)
    {
        $subscription = Subscriber::orderBy('id')->where('sub_id', '=', $id)->where('user_id', '=', $request->user()->id)->first();

        if ($subscription) {
            if($request->user()->id === $subscription->user_id){
                $subscription->delete();
                $response = array(
                    'message' => 'Success',
                    'status' => 200,
                    'data' => Subscriber::orderBy('id', 'asc')->get()
                );
                return response($response, 200);
            }
            else{
                $response = array(
                    'message' => 'Failed - Only the sub creator can delete this sub',
                    'status' => 400
                );
                return response($response, 400);
            }
        } else {
            $response = array(
                'message' => 'Failed',
                'status' => 400
            );
            return response($response, 400);
        }
    }
}
