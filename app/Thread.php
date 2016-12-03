<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = array('sub_id', 'user_id', 'title', 'image', 'text', 'up_votes', 'down_votes', 'user_name', 'total_votes');


    public function sub()
    {
        return $this->belongsTo('App\Sub');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public function votes()
    {
        return $this->hasMany('App\Vote');
    }

    protected $hidden = [
        'password', 'remember_token', 'email',
    ];


}
