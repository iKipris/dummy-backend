<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cases extends Model
{
    protected $fillable = [
        'user_id',
        'case_properties',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
