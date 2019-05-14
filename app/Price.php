<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    protected $fillable = ['for_kg', 'for_place', 'for_kg_brand', 'for_place_brand', 'for_commission', 'client_id', 'created_at'];
}
