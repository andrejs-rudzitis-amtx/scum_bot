<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class chats extends Model
{
    //
    protected $table='chats';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable=[
        'sentAt',
        'context',
        'content',
        'authorSteamId64',
        'authorIgn',
        'authorScumId',
        'mentionAdmins'
    ];
}
