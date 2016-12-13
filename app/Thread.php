<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = array('sub_id', 'user_id', 'title', 'image', 'text', 'user_name');


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
