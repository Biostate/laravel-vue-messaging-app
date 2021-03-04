<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserContact extends Model
{
    use HasFactory;
    protected $guarded = [];
    /**
     * Get the user for the contact.
     */
    public function user()
    {
        return $this->belongsTo('\App\Models\User');
    }

    public function contact()
    {
        return $this->belongsTo('\App\Models\User', 'contact_id',);
    }
}
