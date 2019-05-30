<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['for_kg', 'for_place', 'category_id', 'client_id', 'user_id'];
}
