<?php

namespace App\Http\Controllers;
use App\User;
use App\Thread;
use Illuminate\Http\Request;
use phpDocumentor\Reflection\Types\Object_;
use Illuminate\Support\Facades\DB;

class PublicUserController extends Controller
{
    //
    public function index($id)
    {
        $user = DB::table('users')->where('name', '=', $id)->select('name','id')->first();

        if ($user) {
            $response = array(
                'message' => 'Success',
                'status' => 200,
                'data' => $user
            );
            return response($response, 200);
        }
        else {
            $response = array(
                'message' => 'Failed',
                'status' => 400,
                'errors' => 'No User Found'
            );
            return response($response, 400);
        }
    }


    public function getUserThreads(Request $request, $id)
    {

        $userThreads =  Thread::orderBy('created_at', 'desc')
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
            ->where('threads.user_id', '=',$id)

            ->paginate(25);




        foreach ($userThreads as $thread){
            $thread->total_votes = $thread->up_votes - $thread->down_votes;
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
