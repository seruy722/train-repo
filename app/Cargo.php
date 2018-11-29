<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cargo extends Model
{
    protected $fillable = [
        'type', 'sum', 'sale', 'client', 'place', 'kg', 'fax', 'created_at'
    ];
}
