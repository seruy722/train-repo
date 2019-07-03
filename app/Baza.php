<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Baza extends Model
{
    protected $fillable = ['phone', 'name', 'city', 'code', 'notify'];
}
