<?php

namespace App\Http\Controllers;

use App\Category;
use App\Fax;
use App\FaxPriceForCategory;
use App\PriceForTransporter;
use Illuminate\Http\Request;

class FaxPriceCategoriesData extends Controller
{

    public function getFaxCategoriesData(Request $request)
    {

        $this->validate($request, [
            'faxID' => 'required|numeric',
        ]);

        $data = FaxPriceForCategory::where('fax_id', $request->faxID)->get();

        return response()->json(['status' => true, 'tableCategoriesData' => $data]);
    }

    public function updateFaxCategoriesData(Request $request)
    {
        $this->validate($request, [
            '*.category_id' => 'required|numeric',
            '*.fax_id' => 'required|numeric',
            '*.for_kg' => 'required|numeric',
            '*.category_name' => 'required|string|max:255',
        ]);
        $data = $request->all();
        $faxID = $data[0]['fax_id'];
        $fax = Fax::where('id', $faxID)->first();
        $transporterID = $fax->transporter;

        foreach ($data as $item) {
            $arrvalue = (array)$item;
            $category = Category::firstOrCreate(['category_name' => $arrvalue['category_name']]);
            FaxPriceForCategory::updateOrCreate(['fax_id' => $arrvalue['fax_id'], 'category_id' => $category->id], ['category_id' => $category->id, 'fax_id' => $arrvalue['fax_id'], 'category_price' => $arrvalue['for_kg']]);
            PriceForTransporter::updateOrCreate(['for_kg' => $arrvalue['for_kg'], 'category_id' => $category->id, 'transporter_id'=>$transporterID]);
        }

        $faxCategoriesData = FaxPriceForCategory::where('fax_id', $faxID)->get();

        return response()->json(['status' => true, 'categoriesData' => $faxCategoriesData]);
    }
}
