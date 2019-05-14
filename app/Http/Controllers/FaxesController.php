<?php

namespace App\Http\Controllers;

use App\FaxesMoreInfos;
use App\FaxPriceForCategory;
use App\File;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Fax;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Traits\FormatDate;
use App\Traits\CleanData;

class FaxesController extends Controller
{
    use FormatDate, CleanData;

    public function index()
    {
        $faxesData = DB::table('faxes')
            ->orderBy('date_departure', 'DESC')
            ->join('files', 'faxes.file_id', '=', 'files.id')
            ->select('faxes.*', 'files.file_ext')
            ->get();
//        $faxesData = DB::table('faxes')->orderBy('date_departure', "DESC")->get();
        $arr = $faxesData->toArray();
        foreach ($arr as $item) {
            $item->date_departure = $this->formatDateWithServerTimezone($item->date_departure);
            $item->created_at = $this->formatDateWithServerTimezone($item->created_at);
            $item->updated_at = $this->formatDateWithServerTimezone($item->updated_at);
            $item->uploaded_to_table_cargos_date = $this->formatDateWithServerTimezone($item->uploaded_to_table_cargos_date);
        }

//        $DT = Carbon::now();

        return response()->json(['status' => true, 'faxesData' => $arr, 'date' => (Carbon::createFromTimestamp(strtotime("2019-04-05T20:11:06.306Z")))->format('Y-m-d\TH:i:sP')]);
//        'date' => date("Y-m-d H:i:s", strtotime('2019-04-02T13:38:49+00:00')) (new \DateTime("2019-04-05T22:50:15.728Z"))->format("Y-m-d H:i:s") Carbon::createFromTimestamp(strtotime("2019-04-05T20:11:06.306Z")))->format('Y-m-d\TH:i:sP')
    }

    public function updateData(Request $request)
    {
        $data = $request->all();
        foreach ($data as $item) {
            $arrValue = (object)$item;
            $arrForCreateFax = ['fax_name' => $arrValue->{'fax_name'}, 'date_departure' => $this->formatDateToMySqlDate($arrValue->{'date_departure'}), 'air_or_car' => !!$arrValue->{'air_or_car'}, 'uploaded_to_table_cargos_date' => $this->formatDateToMySqlDate($arrValue->{'uploaded_to_table_cargos_date'}), 'paid' => $arrValue->{'paid'}];
            Fax::where('id', $arrValue->{'id'})->update($arrForCreateFax);
        }
        return response()->json(['status' => true]);
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
        $fileID = $fax->file_id;
        $fileData = File::find($fileID);
        $file = storage_path() . '/app/' . $fileData->url;
        $disposition = 'attachment';
        $headers = array();

        if (Storage::disk('local')->exists($fileData->url)) {
            $response = new BinaryFileResponse($file, 200, $headers, true);

            return $response->setContentDisposition($disposition, $fax->fax_name . '.' . $fileData->file_ext, Str::ascii($fax->fax_name . '.' . $fileData->file_ext));
        }
        return response()->json(['status' => false, 'error' => "Файл с именем <$fax->fax_name.$fileData->file_ext> отсутсвует."]);
    }

    public function destroyFaxes(Request $request)
    {
        $this->validate($request, [
            'faxesIDS' => 'required|array',
        ]);

        $faxesIDS = $request->faxesIDS;

        foreach ($faxesIDS as $id) {
            $fax = Fax::find($id);
            if ($fax) {
                $d2 = FaxesMoreInfos::where('fax_id', $id)->delete();
                $d1 = FaxPriceForCategory::where('fax_id', $id)->delete();
                $d3 = File::where('id', $fax->file_id)->delete();
            }
        }

        Fax::destroy($faxesIDS);

        return response()->json(['status' => true]);
    }
}
