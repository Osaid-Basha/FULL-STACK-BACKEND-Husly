<?php



namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use App\Models\Property;
use App\Models\Negotiation;

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
    Mail::raw("Your verification code is: {$this->two_factor_code}", function ($message) {
        $message->to($this->email)
            ->subject('Your 2FA Code');
    });
}
  public function generateTwoFactorCode()
{
    $this->two_factor_code = rand(100000, 999999);
    $this->two_factor_expires_at = now()->addMinutes(5);
    $this->save();
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

    public function replay ()
    {
        return $this->hasMany(Replay::class);
    }
    public function notifications()
    {
        return $this->belongsToMany(Notification::class, 'user_notifications');

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







}
