<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Notification extends Model
{
    protected $fillable = ['message', 'user_id'];
  // Notification.php
public function users()
{
    return $this->belongsToMany(User::class, 'notification_user')
        ->withPivot('is_read', 'read_at', 'status')
        ->withTimestamps();
}
public static function sendToUser($userId, $type, $message)
    {
        $notification = self::create([
            'type' => $type,
            'message_content' => $message,
        ]);

        $notification->users()->attach($userId, [
            'is_read' => false,
            'status' => 'auto',
            'read_at' => null,
        ]);

        return $notification;
    }

}
