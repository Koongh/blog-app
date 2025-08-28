<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\SoftDeletes;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
        'password' => 'hashed',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function bookmarks()
    {
        return $this->belongsToMany(Post::class, 'bookmarks');
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify((new \App\Notifications\QueueVerifyEmail)->delay(now()->addSeconds(1)));
    }

    public function subscriptions()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscriber_id', 'subscribed_to_id')
                    ->withTimestamps();
    }

    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'subscribed_to_id', 'subscriber_id')
                    ->withTimestamps();
    }
}
