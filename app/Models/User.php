<?php



namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use App\Models\Property;
use App\Models\Notification;
use App\Notifications\ResetPasswordNotification;

use App\Mail\CustomMail;


class User extends Authenticatable
{
   use HasApiTokens, HasFactory, Notifiable;



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
        'remember_token',
        'two_factor_code',
        'two_factor_expires_at',
        'status',
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
    public function sendTwoFactorCodeEmail()
{
   Mail::raw("Your verification code is: {$this->plain_code}", function ($message) {
    $message->to($this->email)
        ->subject('Your 2FA Code');
});

}
public function sendWelcomeEmail()
{
    Mail::to($this->email)->send(new CustomMail(
        '🎉 Welcome to Realstate App!',
        "
        <h2 style='color:#2c3e50;'>Hello {$this->first_name} 👋,</h2>
        <p style='font-size:16px; color:#34495e;'>Welcome to <strong>Realstate App</strong>!<br>We’re excited to have you on board.</p>
        <p style='font-size:15px; color:#7f8c8d;'>You can now explore the best properties, manage your listings, and get instant notifications.</p>
        <p style='margin-top: 30px;'><a href='http://localhost:8080/#/login' style='background-color: #3498db;color: white;padding: 10px 20px;text-decoration: none;border-radius: 6px;font-weight: bold;'>Start Exploring</a></p>
        <p style='margin-top: 40px; font-size:13px; color:#95a5a6;'>If you have any questions, feel free to reply to this email. We're here to help.</p>
        <p style='color:#bdc3c7;'>— The Realstate App Team</p>
        "
    ));
}
public function generateTwoFactorCode()
{
    $code = rand(1000, 9999);
    $this->two_factor_code = bcrypt($code);
    $this->two_factor_expires_at = now()->addMinutes(5);
    $this->save();


    $this->plain_code = $code;
}


public function resetTwoFactorCode()
{
    $this->two_factor_code = null;
    $this->two_factor_expires_at = null;
    $this->save();
}
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
           // 'email_verified_at' => 'datetime',
          //  'password' => 'hashed',
        ];
    }
    public function sendPasswordResetNotification($token)
{
    $this->notify(new ResetPasswordNotification($token));
}
    public function property()
    {
        return $this->hasMany(Property::class);
    }
    public function favorites()
    {
        return $this->hasMany(Favorites::class);
    }


    public function replay ()
    {
        return $this->hasMany(Replay::class);
    }
    // User.php

public function reviews()
{
    return $this->hasMany(\App\Models\Review::class, 'user_id');
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
    return $this->belongsTo(Role::class);
}

   public function negotiations()
{
    return $this->hasMany(Negotiation::class);
}
public function receivedNegotiations()
{
    return $this->hasManyThrough(
        Negotiation::class,
        Property::class,
        'user_id',
        'property_id',
        'id',
        'id'
    );
}
public function notifications()
{
    return $this->belongsToMany(Notification::class, 'notification_user')
        ->withPivot('is_read', 'read_at', 'status')
        ->withTimestamps();
}








}
