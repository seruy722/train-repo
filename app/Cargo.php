<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'type', 'sum', 'sale', 'created_at', 'client_id', 'place', 'kg', 'fax', 'brand', 'notation'
    ];
}
