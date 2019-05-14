<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatDates;

class DebtsController extends Controller
{
    use FormatDates;

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $debtsList = DB::table('debts')->orderBy('created_at', 'DESC')->get();
        $formatedList = $this->needFormatDate($debtsList);

        return response()->json(['status' => true, 'debtsList' => $formatedList]);
    }
}
