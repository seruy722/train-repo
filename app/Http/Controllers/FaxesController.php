<?php

namespace App\Http\Controllers;

use App\FaxesMoreInfos;
use App\FaxPriceForCategory;
use App\PriceForTransporter;
use App\File;
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
    protected $joinFax;

    public function index()
    {
        $faxesData = DB::table('faxes')
            ->orderBy('date_departure', 'DESC')
            ->join('files', 'faxes.file_id', '=', 'files.id')
            ->select('faxes.*', 'files.file_ext')
            ->get();

        $faxesData->each(function ($item) {
            $item->date_departure = $this->formatDateWithServerTimezone($item->date_departure);
            $item->created_at = $this->formatDateWithServerTimezone($item->created_at);
            $item->updated_at = $this->formatDateWithServerTimezone($item->updated_at);
            $item->uploaded_to_table_cargos_date = $this->formatDateWithServerTimezone($item->uploaded_to_table_cargos_date);
        });
//        'date' => (Carbon::createFromTimestamp(strtotime("2019-04-05T20:11:06.306Z")))->format('Y-m-d\TH:i:sP')
        return response()->json(['status' => true, 'faxesData' => $faxesData]);
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
    // Обьеденение факсов
    public function joinFaxes(Request $request)
    {
        $items = $request->all();
        $firstElem = current($items);
        $file = File::create([
            'file_name'=>'JOIN',
            'file_hash_name'=>'JOIN',
            'file_size'=>'JOIN',
            'file_ext'=>'JOIN',
            'url'=>'JOIN',
        ]);
        // Добавление факса
        $this->joinFax = Fax::create([
            'fax_name' => 'JOIN_FAX',
            'date_departure' => date("Y-m-d H:i:s"),
            'paid' => $firstElem['paid'],
            'air_or_car' => $firstElem['air_or_car'],
            'transporter' => $firstElem['transporter'],
            'file_id' => $file->id,
        ]);
        // Добавление цен по категориям в зависимости от факса
        $transporterPrice = PriceForTransporter::where('transporter_id', $firstElem['transporter'])->get();
        if ($transporterPrice->isNotEmpty()) {
            $transporterPrice->each(function ($item) {
                FaxPriceForCategory::create(['fax_id' => $this->joinFax->id, 'category_id' => $item->category_id, 'category_price' => $item->for_kg]);
            });
        }
        // Запись данных новому факсу
        $fData = null;
        foreach ($items as $item) {
            $fData = FaxesMoreInfos::where('fax_id', $item['id'])->get();
            $fData->each(function ($item) {
                $item->fax_id = $this->joinFax->id;
                FaxesMoreInfos::create($item->toArray());
            });
        }
        return response()->json(['fax' => $this->joinFax]);
    }
}
