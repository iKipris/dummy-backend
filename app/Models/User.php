<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function calendarEvents()
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function analytics()
    {
        return $this->hasOne(Analytics::class);
    }

    public function cases()
    {
        return $this->hasMany(Cases::class);
    }

    public function listing()
    {
        return $this->hasOne(Listing::class);
    }

    public function settings()
    {
        return $this->hasOne(Settings::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
