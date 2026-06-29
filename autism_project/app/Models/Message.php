<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [

        'conversation_id',
        'sender_id',
        'sender_type',
        'recipient_id',
        'recipient_type',
        'parent_id',
        'child_id',
        'content',
        'is_read',
        'read_at',
    ];
    public function scopeConversation($query, $senderId, $senderType, $parentId, $parentType)
    {
        return $query->where(function ($q) use ($senderId, $senderType, $parentId, $parentType) {
            $q->where('sender_id', $senderId)
                ->where('sender_type', $senderType)
                ->where('recipient_id', $parentId)
                ->where('recipient_type', $parentType);
        })->orWhere(function ($q) use ($senderId, $senderType, $parentId, $parentType) {
            $q->where('sender_id', $parentId)
                ->where('sender_type', $parentType)
                ->where('recipient_id', $senderId)
                ->where('recipient_type', $senderType);
        });
    }
    public function sender()
    {
        return $this->morphTo(__FUNCTION__, 'sender_type', 'sender_id');
    }

    // Define the recipient relationship
    public function recipient()
    {
        return $this->morphTo(__FUNCTION__, 'recipient_type', 'recipient_id');
    }
}
