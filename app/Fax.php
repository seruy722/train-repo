<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fax extends Model
{
    protected $fillable = ['fax_name', 'date_departure', 'uploaded_to_table_cargos_date', 'uploaded_to_table_cargos'];
}
