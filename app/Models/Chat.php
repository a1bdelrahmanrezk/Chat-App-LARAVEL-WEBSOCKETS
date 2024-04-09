<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Chat extends Model implements HasMedia
{
    use HasFactory,InteractsWithMedia;
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('chat_file');
    }
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'message',
    ];
}
