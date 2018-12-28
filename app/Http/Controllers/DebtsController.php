<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Debts;

class DebtsController extends Controller
{
    public function index()
    {
        $debtsList = Debts::orderBy('created_at', 'DESC')->get();

        return response()->json(['status' => true, 'debtsList' => $debtsList]);
    }
}
