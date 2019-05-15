<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transporter extends Model
{
    protected $fillable = ['name', 'phone', 'notation'];
}
