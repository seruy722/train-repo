<?php

namespace App\Http\Controllers;

use App\Cargo;
use App\Price;
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
        $cargoList = DB::table('cargos')->join('users', 'cargos.client_id', '=', 'users.id')->select('cargos.*', 'users.name')->orderBy('created_at', 'DESC')->take(1000)->get();
//        $cargoList = Cargo::leftJoin('users', 'cargos.client_id', '=', 'users.id')->select('cargos.*', 'users.name')->orderBy('created_at', 'DESC')->get();
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
     * @param  \Illuminate\Http\Request $request
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

//        $this->validate($request, [
//            '*.type' => 'required|string|max:255',
//            '*.sum' => 'numeric|digits_between:1,8',
//            '*.sale' => 'numeric|digits_between:1,8',
//            '*.client_id' => 'required|max:255',
//            '*.place' => 'numeric|digits_between:1,8',
//            '*.kg' => 'numeric|digits_between:1,8',
//            '*.fax' => 'nullable|max:255',
//            '*.brand' => 'boolean',
//            '*.notation' => 'string|max:255',
//            '*.created_at' => 'max:255',
//        ]);


        $allData = $request->all();
        $client = $allData[0]['client'];
        $type = $allData[0]['type'];
        $userPriceObj = Price::where('client_id', $client->id)->first();
        $arr = [];
        switch ($type) {
            case 'ОПЛАТА':

                break;
            case 'ДОЛГ':

                foreach ($allData as $value) {
                    $arrData = (array)$value;

                    $cleanData = $this->clean($arrData);

                    $cleanData['sum'] = round(($cleanData['kg'] * $userPriceObj->for_kg + $cleanData['place'] * $userPriceObj->for_place) * -1);

                    $savedCargoEntry = Cargo::create($cleanData);
                    array_push($arr, $savedCargoEntry);

                }
                break;
            default:
                return response()->json(['status' => 'error', 'message' => 'Неправильный тип: ' . $type]);
        }

        return response()->json(['status' => true, 'cargoEntry' => $arr]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
