<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PriceForTransporter extends Model
{
    protected $fillable = ['for_kg', 'transporter_id', 'category_id'];
}
