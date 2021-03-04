<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FriendshipRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function from_user(){
       return $this->hasOne(User::class, 'id', 'from');
    }

    public function to_user(){
        return $this->hasOne(User::class, 'id', 'to');
    }
}
