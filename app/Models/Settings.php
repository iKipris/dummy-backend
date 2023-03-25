<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'user_id',
        'timezone',
        'language',
        'currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
