<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'message_content',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('is_read', 'read_at', 'status')
            ->withTimestamps();
    }

    public static function sendToUser(int $userId, string $type, string $message, ?string $status = null): self
    {
        $notification = self::create([
            'type' => $type,
            'message_content' => $message,
        ]);

        $notification->users()->attach($userId, [
            'is_read' => false,
            'read_at' => null,
            'status' => $status,
        ]);

        return $notification;
    }

    public static function sendToMultipleUsers(array $userIds, string $type, string $message, ?string $status = null): self
    {
        $notification = self::create([
            'type' => $type,
            'message_content' => $message,
        ]);

        $attachData = [];
        foreach ($userIds as $id) {
            $attachData[$id] = [
                'is_read' => false,
                'read_at' => null,
                'status' => $status,
            ];
        }
        if (!empty($attachData)) {
            $notification->users()->attach($attachData);
        }

        return $notification;
    }
}


