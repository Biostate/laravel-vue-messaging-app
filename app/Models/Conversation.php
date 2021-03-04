<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function participants()
    {
        return $this->hasMany(\App\Models\Participant::class);
    }

    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class);
    }

    public function getLatestMessageAttribute(){
        return $this->messages()->latest()->first();
    }

    public function getIsHaveUnreadedMessageAttribute(){

    }
}
