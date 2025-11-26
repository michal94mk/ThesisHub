<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'thesis_id',
        'user_id',
        'message',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'read_at' => 'datetime',
    ];

    /**
     * Get the thesis that owns the message.
     */
    public function thesis(): BelongsTo
    {
        return $this->belongsTo(Thesis::class);
    }

    /**
     * Get the user who sent the message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if message has been read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Mark message as read.
     */
    public function markAsRead(): void
    {
        if (!$this->isRead()) {
            $this->read_at = now();
            $this->save();
        }
    }

    /**
     * Scope to get unread messages for a user.
     */
    public function scopeUnreadFor($query, int $userId)
    {
        return $query->where('user_id', '!=', $userId)
                     ->whereNull('read_at');
    }

    /**
     * Scope to get messages for a thesis.
     */
    public function scopeForThesis($query, int $thesisId)
    {
        return $query->where('thesis_id', $thesisId);
    }
}
