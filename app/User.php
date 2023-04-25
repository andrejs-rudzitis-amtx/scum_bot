<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class User extends Model
{
    use Notifiable;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'discordId',
        'scumId',
        'steamId64',
        'welcomePackReceived',
        'balance',
        'createdAt',
        'updatedAt',
        'presence',
        'ign',
        'presenceUpdatedAt',
        'squadScumId',
        'squadRank',
        'squadUpdatedAt',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'welcomePackReceived' => 'datetime',
        'presenceUpdatedAt' => 'datetime',
        'squadUpdatedAt' => 'datetime',
    ];
}
