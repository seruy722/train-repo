<?php

namespace App\Http\Controllers;

use App\Cargo;
use Illuminate\Http\Request;


class CargoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cargoList = Cargo::orderBy('created_at', 'DESC')->get();

        return response()->json(['status' => true, 'cargoList' => $cargoList]);
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
        $arr = [];
        foreach ($request->all() as $value) {
//            return response()->json(['status', true, 'cargoEntry'=> $value]);



            $arrData = (array)$value;

            $cleanData = $this->cleanData($arrData);
//            return response()->json(['status', true, 'cargoEntry'=> $arrData]);

            $savedCargoEntry = Cargo::create($cleanData);
            array_push($arr, $savedCargoEntry);

        }
        return response()->json(['status'=>true, 'cargoEntry'=> $arr]);
//        return response()->json(['status', true, 'cargoEntry'=> $arrData]);
    }

    public function cleanData($value)
    {
        $arr = array_map("trim", $value);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map("stripcslashes", $arr);
        return $arr;
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
