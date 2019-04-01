<?php

namespace App\Http\Controllers;

use App\FaxesMoreInfos;
use App\FaxPriceForCategory;
use Illuminate\Http\Request;
use App\Fax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FaxesController extends Controller
{

    public function index()
    {
        $faxesData = DB::table('faxes')->orderBy('date_departure', "DESC")->get();
        $arr = $faxesData->toArray();
        foreach ($arr as $item) {
            $item->date_departure = date("c", strtotime($item->date_departure));
            $item->created_at = date("c", strtotime($item->created_at));
        }

        return response()->json(['status' => true, 'faxesData' => $faxesData, 'date'=>date('c')]);
    }

    public function getFaxesNames()
    {
        $faxesNames = DB::table('faxes')->select('id', 'fax_name')->get();
        return response()->json(['status' => true, 'faxesNames' => $faxesNames]);
    }


    public function downloadOriginalFax(Request $request)
    {
        $this->validate($request, [
            'fax_id' => 'required',
        ]);

        $faxID = (int)$request->fax_id;
        $fax = Fax::findOrFail($faxID);
        $faxName = $fax->fax_name;
        $file = storage_path() . '/app/faxes/' . $faxName;
        $disposition = 'attachment';
        $headers = array();

        if (Storage::disk('local')->exists('faxes/' . $faxName)) {
            $response = new BinaryFileResponse($file, 200, $headers, true);

            return $response->setContentDisposition($disposition, $faxName, Str::ascii($faxName));
        }
        return response()->json(['status' => false, 'error' => "Файл с именем <$faxName> отсутсвует."]);
    }

    public function destroyFaxes(Request $request)
    {
        $this->validate($request, [
            'faxesIDS' => 'required|array',
        ]);

        $faxesIDS = $request->faxesIDS;

        Fax::destroy($faxesIDS);

        foreach ($faxesIDS as $value) {
            FaxesMoreInfos::where('fax_id', $value)->delete();
            FaxPriceForCategory::where('fax_id', $value)->delete();
        }

        return response()->json(['status' => true]);
    }
}
