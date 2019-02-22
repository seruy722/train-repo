<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fax;
use App\Utils\FormatDatesClass;
use Illuminate\Support\Facades\DB;

class FaxesController extends Controller
{

    public function index()
    {
        $FormatDatesClass = new FormatDatesClass('d-m-y');
        $faxesData = $cargoList = DB::table('faxes')->orderBy('date_departure', "DESC")->take(1000)->get();

        $formatedDatesArr = $FormatDatesClass->formatDatesFields($faxesData->toArray(), ['created_at', 'date_departure', 'uploaded_to_table_cargos_date']);

        return response()->json(['status' => true, 'faxesData' => $formatedDatesArr]);
    }
}
