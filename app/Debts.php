<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debts extends Model
{
    protected $fillable = [
        'type', 'sum', 'client', 'created_at', 'commission', 'notation'
    ];
}
