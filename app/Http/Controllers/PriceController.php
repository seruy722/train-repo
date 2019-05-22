<?php

namespace App\Http\Controllers;

use App\Price;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceController extends Controller
{
    public function index()
    {
        $faxData = DB::table('prices')
            ->join('users', 'prices.client_id', '=', 'users.id')
//            ->join('categories', 'prices.category_id', '=', 'categories.id')
            ->select('prices.*', 'users.name')
            ->get();



        return response()->json(['status' => true, 'priceData' => $faxData->groupBy('name')]);
    }

}
