<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'user_id',
        'updated_at',
        'first_name',
        'last_name',
        'phone_number',
        'avatar_link',
        'zip_code',
        'city',
        'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
