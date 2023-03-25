<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    protected $fillable = [
        'user_id',
        'visitors',
        'page_views',
        'bounce_rate',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
