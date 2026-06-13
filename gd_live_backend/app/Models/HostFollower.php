<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HostFollower extends Model
{
    protected $fillable = [
        'host_id',
        'user_id',
        'notify_when_online',
        'last_online_notified_at',
    ];

    protected $casts = [
        'notify_when_online' => 'boolean',
        'last_online_notified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(Host::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
