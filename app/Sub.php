<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sub extends Model
{
    protected $fillable = array('sub_name', 'description', 'user_id');

    public function sub_creator()
    {
        return $this->belongsTo('App\User');
    }


    public function threads()
    {
        return $this->hasMany('App\Thread');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

}
