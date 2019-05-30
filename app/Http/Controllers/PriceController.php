<?php

namespace App\Http\Controllers;

use App\Price;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FormatDate;

class PriceController extends Controller
{
    use FormatDate;

    public function index()
    {
        $faxData = DB::table('prices')
            ->join('users', 'prices.client_id', '=', 'users.id')
//            ->join('users', 'prices.user_id', '=', 'users.id')
//            ->join('categories', 'prices.category_id', '=', 'categories.id')
            ->select('prices.*', 'users.name')
            ->get();

        $faxData->each(function ($item) {
            $item->updated_at = $this->formatDateWithServerTimezone($item->updated_at);
        });

        return response()->json(['status' => true, 'priceData' => $faxData->groupBy('name')]);
    }

    public function updatePriceData(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'client_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'for_kg' => 'required|numeric',
            'for_place' => 'required|numeric',
        ]);

        $price = $request->only('id', 'category_id', 'for_kg', 'for_place', 'user_id');
//        return response()->json(['status' => $request->all()]);

        $updatedEntry = Price::find($price['id']);
        if($updatedEntry){
            $updatedEntry->update([
                'category_id' => $price['category_id'],
                'for_kg' => $price['for_kg'],
                'for_place' => $price['for_place'],
                'user_id' => $user->id,
            ]);
            return response()->json(['status' => true, 'updatedEntry' => $updatedEntry]);
        }

        return response()->json(['status' => false]);
    }

    public function destroyPrice(Request $request)
    {
        $entryID = $request->ID;
        $entry = Price::find($entryID);

        if ($entry) {
            $entry->delete();
            return response()->json(['status' => true]);
        }

        return response()->json(['status' => false]);
    }

    public function addPriceData(Request $request)
    {
        $this->validate($request, [
            'client_id' => 'required|numeric',
            'category_id' => 'required|numeric',
            'for_kg' => 'required|numeric',
            'for_place' => 'required|numeric',
        ]);



        $price = $request->all();
        $check = Price::where('client_id', $price['client_id'])->where('category_id', $price['category_id'])->first();
        if(!$check){
            $user = $request->user();

            $entry = Price::create([
                'category_id' => $price['category_id'],
                'client_id' => $price['client_id'],
                'for_kg' => $price['for_kg'],
                'for_place' => $price['for_place'],
                'user_id' => $user->id,
            ]);

            return response()->json(['status' => true, 'priceEntry' => $entry]);
        }

        return response()->json(['status' => false, 'answer'=> 'Запись уже существует']);
    }
}
