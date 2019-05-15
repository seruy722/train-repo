<?php

namespace App\Http\Controllers;

use App\Transporter;
use Illuminate\Http\Request;

class TransportersController extends Controller
{
    public function index()
    {
        $transporters = Transporter::all();
        return response()->json(['status' => true, 'transporters' => $transporters]);
    }

//    public function getTransporter(Request $request)
//    {
//        $transporterID = $request->id;
//        $transporter = Transporter::where('id', $transporterID)->first();
//    }
}
