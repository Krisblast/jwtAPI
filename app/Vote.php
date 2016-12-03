<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $fillable = array('vote_type', 'user_id', 'thread_id', 'comment_id');


    public function user()
    {
        return $this->belongsTo('App\User');
    }


    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }


    public function comment()
    {
        return $this->belongsTo('App\Comment');
    }

}
