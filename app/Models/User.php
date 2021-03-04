<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function contacts(){
        return $this->hasMany(UserContact::class);
    }

    public function conversations(){
        return $this->hasMany(Participant::class, 'id', 'user_id');
    }

    public function frendship_requests(){
        return $this->hasMany(FriendshipRequest::class, 'to');
    }

    public function getFriendshipRequestsCountAttribute(){
        return $this->frendship_requests()->count();
    }

    public function canJoinRoom($conversation_id)
    {
        return \App\Models\Participant::where([['user_id', $this->id], ['conversation_id', $conversation_id]])->exists();
    }
}
