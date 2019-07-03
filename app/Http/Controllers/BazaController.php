<?php

namespace App\Http\Controllers;

use App\Baza;
use Illuminate\Http\Request;

class BazaController extends Controller
{
    public function index()
    {
        return response()->json(['baza' => Baza::all()]);
    }

    public function updateData(Request $request)
    {
        $this->validate($request, [
            '*.name' => 'max:255',
            '*.phone' => 'max:255',
            '*.code' => 'max:255',
            '*.city' => 'max:255',
            '*.notify' => 'boolean',
        ]);

        $data = $request->toArray();

        foreach ($data as $item) {
            Baza::where('id', $item['id'])->update([
                'name' => $item['name'],
                'phone' => $item['phone'],
                'code' => $item['code'],
                'city' => $item['city'],
                'notify' => $item['notify'],
            ]);
        }

        return response()->json(['status' => true]);
    }

    public function destroyData(Request $request)
    {
        $data = $request->toArray();
        Baza::destroy($data);
        return response()->json(['status' => true]);
    }
}
