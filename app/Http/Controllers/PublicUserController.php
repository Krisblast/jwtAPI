<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Object_;
use Illuminate\Support\Facades\DB;

class PublicUserController extends Controller
{
    //
    public function index($id)
    {
        $user_response = New Object_();
        $user = DB::table('users')->where('name', '=', $id)->first();

        $user_response->name = $user->name;
        $user_response->id = $user->id;
        if ($user) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $user_response
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


    public function getUserThreads($id)
    {
        $userThreads = DB::table('threads')->where('user_id', '=', $id)->get();
        foreach ($userThreads as $thread) {
            $commentCount = DB::table('comments')->where('thread_id', '=', $thread->id)->count();
            $thread->comment_count = $commentCount;
        }
        if ($userThreads) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $userThreads
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
}
