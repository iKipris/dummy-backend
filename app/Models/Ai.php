<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ai extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'chats',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
