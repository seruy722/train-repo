<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'type', 'sum', 'sale', 'client_id', 'place', 'kg', 'fax', 'brand', 'notation'
    ];
}
