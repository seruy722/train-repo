<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\BlackList;
use Illuminate\Support\Facades\Storage;

class BlackListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = BlackList::orderBy('created_at', 'desc')->get();
        return response()->json(['data'=> $data]);
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
        $array = (array)$request->only('fio','phone_1','phone_2','phone_3','foto','notation');
        $this->cleanData($array);

        $type = $request->type;
        $file = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Storage::disk('images')->put('/client', $file);
            $array['foto'] = $fileName;
        }

        switch ($type) {
            case 'save':
                $this->validate($request, [
                    'fio' => 'required|string|unique:black_lists|max:255',
                    'phone_1' => 'min:10|max:255',
                    'phone_2' => 'min:10|max:255',
                    'phone_3' => 'min:10|max:255',
                    'notation' => 'max:255',
                ]);
                if (!$file) {
                    $array['foto'] = 'nofoto.jpg';
                }
                $item = BlackList::create($array);
                return response()->json(['status' => true, 'type' => 'save', 'item' => $item]);
                break;

            case 'update':
                $itemID = $request->id;
                $this->validate($request, [
                    'fio' => 'required|string|max:255|unique:black_lists,fio,' .$itemID,
                    'phone_1' => 'min:10|max:255',
                    'phone_2' => 'min:10|max:255',
                    'phone_3' => 'min:10|max:255',
                    'notation' => 'max:255',
                ]);

                $item = BlackList::findOrFail($itemID);

                if ($file) {
                    $fileName = substr($item->foto, strpos($item->foto, '/') + 1);
                    $exists = Storage::disk('images')->exists('/client/' . $fileName);

                    if ($exists) {
                        Storage::disk('images')->delete('/client/' . $fileName);
                    }
                }

                BlackList::where('id', $itemID)->update($array);
                $item = BlackList::find($itemID);
                return response()->json(['status' => true, 'type' => 'update', 'item' => $item]);
                break;
        }
    }

    public function cleanData($value)
    {
        $arr = array_map("trim", $value);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map("stripcslashes", $arr);
        return $arr;
    }

    public function deleteFoto(Request $request)
    {
        $item = BlackList::find($request->id);
        $fileName = substr($item->foto, strpos($item->foto, '/') + 1);
        $exists = Storage::disk('images')->exists('/client/' . $fileName);

        if ($exists) {
            Storage::disk('images')->delete('/client/' . $fileName);
        }
        $item->foto = 'nofoto.jpg';
        $item->save();
        return response()->json(['status' => true, 'item' => $item]);
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
    public function update(Request $request)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $item = BlackList::findOrFail($request->id);
        $fileName = substr($item->foto, strpos($item->foto, '/') + 1);
        $exists = Storage::disk('images')->exists('/client/' . $fileName);

        if ($exists) {
            Storage::disk('images')->delete('/client/' . $fileName);
        }

        $item->delete();

        return response()->json(['status' => true]);
    }
}
