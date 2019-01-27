<?php

namespace App\Http\Controllers;

use App\Cargo;
use App\Traits\CleanData;
use App\Traits\NumberFormat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatDates;


class CargoController extends Controller
{
    use FormatDates, CleanData, NumberFormat;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cargoList = DB::table('cargos')->orderBy('created_at', 'DESC')->get();
        $formatDateList = $this->needFormatDate($cargoList);
        $formatNumberList = $this->prettyFormat($formatDateList);

        return response()->json(['status' => true, 'cargoList' => $formatNumberList]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return response()->json(['status' => true,'item' => $request->all()]);
//        $arr = [];
//        $this->validate($request, [
//            'type' => 'required|string|max:255',
//            'sum' => 'min:10|max:255',
//            'sale' => 'min:10|max:255',
//            'client' => 'required|min:10|max:255',
//            'place' => 'max:255',
//            'kg' => 'max:255',
//            'fax' => 'max:255',
//            'created_at' => 'max:255',
//        ]);

        $this->validate($request, [
            '*.type' => 'required|string|max:255',
            '*.sum' => 'numeric|digits_between:1,8',
            '*.sale' => 'numeric|digits_between:1,8',
            '*.client' => 'required|max:255',
            '*.place' => 'numeric|digits_between:1,8',
            '*.kg' => 'numeric|digits_between:1,8',
            '*.fax' => 'nullable|max:255',
            '*.notation' => 'string|max:255',
            '*.created_at' => 'max:255',
        ]);

        $arr = [];
        foreach ($request->all() as $value) {
            $arrData = (array)$value;

            $cleanData = $this->clean($arrData);

            $savedCargoEntry = Cargo::create($cleanData);
            array_push($arr, $savedCargoEntry);

        }
        return response()->json(['status'=>true, 'cargoEntry'=> $arr]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
