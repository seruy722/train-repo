<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FaxPriceForCategory extends Model
{
    protected $fillable = ['fax_id', 'category_price', 'category_id', 'fax_categories_data'];
}
