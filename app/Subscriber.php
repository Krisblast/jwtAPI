<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = array('sub_id', 'user_id');

    protected $hidden = [
        'password', 'remember_token', 'email',
    ];


    public function user_subscriber()
    {
        return $this->belongsTo('App\User');
    }

    public function sub()
    {
        return $this->belongsTo('App\Sub');
    }


}
