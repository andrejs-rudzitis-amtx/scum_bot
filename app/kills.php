<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class kills extends Model
{
    //
    protected $table='kills';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable=[
        'killerName',
        'killerInEvent',
        'killerServerX',
        'killerServerY',
        'killerServerZ',
        'killerClientX',
        'killerClientY',
        'killerClientZ',
        'killerSteamId64',
        'killerImmortal',
        'victimName',
        'victimServerX',
        'victimServerY',
        'victimServerZ',
        'victimClientX',
        'victimClientY',
        'victimClientZ',
        'victimSteamId64',
        'weaponName',
        'weaponDamage',
        'timeOfDay',
        'logTimeStamp',
        'createdAt',
        'updatedAt',
        'victimUserId',
        'killerUserId',
        'distance',
    ];
}
