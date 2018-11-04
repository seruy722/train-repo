<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlackList extends Model
{
    protected $fillable = [
        'fio', 'phone_1','phone_2','phone_3', 'email', 'foto', 'notation'
    ];
}
