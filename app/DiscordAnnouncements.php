<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DiscordAnnouncements extends Model
{
    //
    protected $table = 'discordAnnouncements';

    protected $fillable = [
        'sentAt',
        'content',
        'publishedAt',
    ];
}
