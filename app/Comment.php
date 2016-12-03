<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = array('sub_id', 'user_id', 'comment_text', 'user_name', 'thread_id', 'up_votes', 'down_votes', 'total_votes', 'comment_ref_id');

    public function thread()
    {
        return $this->belongsTo('App\Thread');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function sub()
    {
        return $this->belongsTo('App\Sub');
    }

    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    protected $hidden = [
        'password', 'remember_token', 'email',
    ];
}
