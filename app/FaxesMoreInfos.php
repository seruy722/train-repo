<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaxesMoreInfos extends Model
{
    protected $table = 'faxes_more_info';

    protected $fillable = ['code', 'client_id', 'fax_id', 'place', 'kg', 'brand', 'shop', 'name_of_things', 'notation'];
}
