<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];



    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();  // Eloquent model method
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'id' => $this->id,

             ]
        ];
    }


    public function subs()
    {
        return $this->hasMany('App\Sub');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Subscriber');
    }

    public function threads()
    {
        return $this->hasMany('App\Thread');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }


    public function votes()
    {
        return $this->hasMany('App\Vote');
    }



    function hasVoted($request, $id)
    {
        $hasVoted = DB::table('votes')
            ->where('thread_id', '=', $id)
            ->where('user_id', '=', $request->user()->id)
            ->first();

        return $this->hasMany('App\Vote');
    }
}
