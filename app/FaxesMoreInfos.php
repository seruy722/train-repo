<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaxesMoreInfos extends Model
{
    protected $table = 'faxes_more_info';

    protected $fillable = ['code', 'client_id', 'fax_id', 'place', 'kg', 'brand', 'shop', 'list_things', 'notation', 'for_kg', 'for_place', 'category_id', 'created_at', 'updated_at'];
}
