<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',

        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function property()
    {
        return $this->hasMany(Property::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorites::class);
    }
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
    public function buying()
    {
        return $this->hasMany(BuyingRequest::class);
    }
    public function replay ()
    {
        return $this->hasMany(Replay::class);
    }
    public function notifaction()
    {
        return $this->hasMany(Notification::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }




}
