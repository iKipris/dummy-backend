<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'listing_data',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
