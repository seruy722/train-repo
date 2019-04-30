<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fax extends Model
{
    protected $fillable = ['file_id', 'fax_name', 'date_departure', 'uploaded_to_table_cargos_date', 'air_or_car', 'paid'];
}
