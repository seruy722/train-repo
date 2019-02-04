<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Fax;
use Illuminate\Support\Facades\DB;

class FaxesController extends Controller
{

    public function index(){
        $faxesData = DB::table('faxes')->orderBy('upload_date', "DESC")->get();

        foreach ($faxesData as $item) {
            $item->created_at = date("d-m-Y", strtotime($item->created_at));
            $item->upload_date = date("d-m-Y", strtotime($item->upload_date));
            $item->uploaded_to_table_cargos_date = date("d-m-Y", strtotime($item->uploaded_to_table_cargos_date));
        }

        return response()->json(['status' => true, 'faxesData' => $faxesData]);
    }
}
