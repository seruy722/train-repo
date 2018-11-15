<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Emails;

class EmailsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $emails = Emails::orderBy('email', 'ASC')->get();
        return response()->json(['status' => true, 'emails' => $emails]);
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
        $this->validate($request, [
            'email' => 'required|email|max:255|',
            'notation' => 'max:255',
        ]);
        $array = (array)$request->only('email', 'notation');
        $type = $request->type;

        switch ($type) {
            case 'save':
                $this->validate($request, [
                    'email' => 'unique:emails,email,'
                ]);
                $cleanedArray = $this->cleanData($array);
                $email = Emails::create($cleanedArray);

                return response()->json(['status' => true, 'type' => 'save', 'email' => $email]);
                break;

            case 'update':
                $itemID = $request->id;
                $this->validate($request, [
                    'email' => 'unique:emails,email,' . $itemID,
                ]);

                $cleanedArray = $this->cleanData($array);
                $email = Emails::findOrFail($itemID);
                $email->update($cleanedArray);

                return response()->json(['status' => true, 'type' => 'update', 'email' => $email]);
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
    public function destroy(Request $request)
    {
        $email = Emails::findOrFail($request->id);
        $email->delete();

        return response()->json(['status'=> true]);
    }
}
