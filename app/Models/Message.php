<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function conversation(){
        return $this->hasOne('\App\Models\Conversation', 'id', 'conversation_id');
    }

    public function getToAttribute(){
        return $this->conversation->participants()->where('user_id', '!=', $this->user_id)->first();
    }
}
