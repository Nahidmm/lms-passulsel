<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'title', 'body', 'type', 'icon', 'url', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Static helper to send a notification to one or more users.
     *
     * @param  int|array  $userIds
     */
    public static function kirim(
        int|array $userIds,
        string $title,
        string $body = '',
        string $type = 'info',
        string $icon = 'bell',
        string $url = ''
    ): void {
        $userIds = is_array($userIds) ? $userIds : [$userIds];

        $rows = array_map(fn($id) => [
            'user_id'    => $id,
            'title'      => $title,
            'body'       => $body,
            'type'       => $type,
            'icon'       => $icon,
            'url'        => $url,
            'read_at'    => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $userIds);

        self::insert($rows);
    }

    /**
     * Send a notification to all admin/superadmin users.
     */
    public static function kirimKeAdmin(
        string $title,
        string $body = '',
        string $type = 'info',
        string $icon = 'bell',
        string $url = ''
    ): void {
        $adminIds = User::whereIn('role', ['admin', 'superadmin'])
            ->pluck('id')
            ->toArray();

        if (!empty($adminIds)) {
            self::kirim($adminIds, $title, $body, $type, $icon, $url);
        }
    }
}
