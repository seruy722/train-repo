<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exports\FaxesDataExport;
use App\Price;
use Illuminate\Http\Request;
use App\Imports\FaxesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\FaxesMoreInfos;
use App\Fax;
use App\User;
use App\Traits\FormatDateToServerDate;
use App\Traits\CleanData;
use App\Utils\FormatDatesClass;

class FaxesMoreInfosController extends Controller
{
    use CleanData, FormatDateToServerDate;

    public function dataForFaxCounted(Request $request)
    {
        $this->validate($request, [
            'faxID' => 'required|integer',
        ]);

        return response()->json(['status' => true, 'groupedData' => $this->sampledData($request->faxID)]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'uploadedFile' => 'required|file|max:10240||mimes:xls,xlsx,csv',
            'faxName' => 'required|string|min:3|max:255',
            'dateOfDeparture' => 'required|date|max:255',
            'transport' => 'required|numeric'
        ]);

        // Класс доп функций
        $FormatDatesClass = new FormatDatesClass('d-m-y');
        $file = $request->file('uploadedFile');

        $cleanedData = $this->clean((array)$request->all());

        // Сохранение файла на сервере
        if ($request->hasFile('uploadedFile')) {
            $fileExtention = $file->getClientOriginalExtension();
            $fileName = $request->faxName . '.' . $fileExtention;


            if (Storage::disk('local')->exists('faxes/' . $fileName)) {
                return response()->json(['status' => false, 'fileName' => $fileName, 'pathToFile' => Storage::url($fileName), 'error' => 'Файл с таким именем уже существует.']);
            } else {
                Storage::putFileAs('faxes', $file, $fileName);
            }
        }

        // Добавление факса
        $fileExtention = $request->file('uploadedFile')->getClientOriginalExtension();
        $arrForCreateFax = ['fax_name' => $cleanedData['faxName'] . '.' . $fileExtention, 'date_departure' => $FormatDatesClass->formatServerDate($cleanedData['dateOfDeparture']), 'air_or_car' => !!$cleanedData['transport']];
        $fax = Fax::create($arrForCreateFax);

        $formatedDatesFax = $FormatDatesClass->formatDatesFields($fax->toArray(), ['created_at', 'date_departure']);

        // Загрузка данных файла на сервер
        $ImportedFaxArray = Excel::toArray(new FaxesImport, $file);
        foreach ($ImportedFaxArray as $item) {
            foreach ($item as $key => $elem) {
                $cleanedElem = $this->clean($elem);
                // Пропускаем первые 2 строки
                if ($key === 0 || $key === 1) {
                    continue;
                }
                // Если все поля пустые выходим из итерации
                if (!$cleanedElem[0] && !$cleanedElem[1] && !$cleanedElem[2] && !$cleanedElem[3] && !$cleanedElem[4] && !$cleanedElem[5] && !$cleanedElem[6] && !$cleanedElem[7]) {
                    break;
                }
                // Если первые 5 полей пустые - то дописываем к предыдущей записи товары
                if (!$cleanedElem[0] && !$cleanedElem[1] && !$cleanedElem[2] && !$cleanedElem[3] && !$cleanedElem[4]) {
                    $lastEntry = FaxesMoreInfos::orderBy('id', 'desc')->first();
                    $things = json_decode($lastEntry->name_of_things);
                    $things->{$cleanedElem[5]} = $cleanedElem[6];
                    $lastEntry->name_of_things = json_encode($things);
                    $lastEntry->save();
                } else {
                    // Обрезаем из имени клиента приставку 007/
                    $startPos = stripos($cleanedElem[1], '007/');
                    $userName = substr($cleanedElem[1], $startPos + 4);
                    if (!is_numeric($startPos)) {
                        $userName = $cleanedElem[1];
                    }

                    $client = User::firstOrCreate(['name' => $userName], ['password' => 'default']);
                    $prices = Price::where('client_id', $client->id)->first();
//                    $category = Price::where('category_name', $client->id)->first();

                    if ($cleanedElem[7] === 'Бренд') {
                        $forKg = $prices->for_kg_brand;
                        $forPlace = $prices->for_place_brand;
                    } else {
                        $forKg = $prices->for_kg;
                        $forPlace = $prices->for_place;
                    }

//                    return response()->json(['status' => false, 'elem7'=> $cleanedElem[7]]);
                    try {
                        FaxesMoreInfos::create([
                            'code' => (string)$cleanedElem[0],
                            'place' => (int)$cleanedElem[2],
                            'kg' => (float)$cleanedElem[3],
                            'client_id' => (int)$client->id,
                            'fax_id' => (int)$fax->id,
                            'brand' => $cleanedElem[7] === 'Бренд',
                            'shop' => (string)$cleanedElem[4],
                            'for_kg' => (float)$forKg,
                            'for_place' => (float)$forPlace,
                            'name_of_things' => json_encode([$cleanedElem[5] => (int)$cleanedElem[6]]),
                            'notation' => (string)$cleanedElem[7]
                        ]);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'error' => 'Ошибка при записи данных.', 'row' => $cleanedElem, 'exception' => $e]);
                    }
                }
            }
        }
        $fullFaxInfo = FaxesMoreInfos::where('fax_id', $fax->id)->get();
        return response()->json(['status' => true, 'fax' => $formatedDatesFax, 'fullFaxInfo' => $fullFaxInfo]);
    }


    public function updateFaxData(Request $request)
    {
//        return response()->json(['status' => false, 'ARR'=>$request->all()]);
        $this->validate($request, [
            '*.code' => 'required|string|max:255',
            '*.client_id' => 'required|numeric',
            '*.category_id' => 'required|numeric',
            '*.fax_id' => 'required|numeric',
            '*.place' => 'required|numeric',
            '*.kg' => 'required|numeric',
            '*.shop' => 'max:255',
            '*.name_of_things' => 'required|json',
            '*.brand' => 'required|boolean',
            '*.for_kg' => 'required|numeric',
            '*.for_place' => 'required|numeric',
            '*.notation' => 'max:255',
        ]);

        $data = $request->all();
        $faxID = $data[0]['fax_id'];

//        return response()->json(['status' => false, 'ARR'=>$data]);
        foreach ($data as $value) {
            $arrvalue = (array)$value;
            $client = User::where('id', $arrvalue['client_id'])->where('name', $arrvalue['name'])->first();
            $category = Category::firstOrCreate(['category_name' => $arrvalue['category_name']]);

            if ($client) {
                $clientID = (int)$arrvalue['client_id'];
            } else {
                $client = User::firstOrCreate(['name' => $arrvalue['name']], ['password' => 'default']);
                $clientID = $client->id;
            }

            FaxesMoreInfos::where('id', $arrvalue['id'])->update([
                'code' => (string)$arrvalue['code'],
                'place' => (int)$arrvalue['place'],
                'kg' => (float)$arrvalue['kg'],
                'client_id' => $clientID,
                'category_id' => $category->id,
                'fax_id' => (int)$arrvalue['fax_id'],
                'brand' => (boolean)$arrvalue['brand'],
                'shop' => (string)$arrvalue['shop'],
                'for_kg' => (float)$arrvalue['for_kg'],
                'for_place' => (float)$arrvalue['for_place'],
                'name_of_things' => $arrvalue['name_of_things'],
                'notation' => (string)$arrvalue['notation']
            ]);
        }

        return response()->json(['status' => true, 'groupedData' => $this->sampledData($faxID)]);
    }

    public function sampledData($faxID)
    {
        $faxData = DB::table('faxes_more_info')
            ->where('fax_id', $faxID)
            ->join('users', 'faxes_more_info.client_id', '=', 'users.id')
            ->join('categories', 'faxes_more_info.category_id', '=', 'categories.id')
            ->select('faxes_more_info.*', 'users.name', 'categories.category_name')
            ->get();

        return $faxData->groupBy('client_id');
    }

    public function destroyFaxEntries(Request $request)
    {
        $this->validate($request, [
            'entriesID' => 'required|array',
        ]);

        $arrayEntriesID = $request->entriesID;

        FaxesMoreInfos::destroy($arrayEntriesID);

        return response()->json(['status' => true]);
    }

    public function changeEntriesFaxID(Request $request)
    {
        $this->validate($request, [
            'entriesID' => 'required|array',
            'faxID' => 'required|numeric',
        ]);

        $faxEntries = $request->entriesID;
        $faxID = $request->faxID;

        foreach ($faxEntries as $value) {
            FaxesMoreInfos::where('id', $value)->update([
                'fax_id' => $faxID
            ]);
        }

        return response()->json(['status' => true]);

    }

    public function export()
    {
        return Excel::download(new FaxesDataExport(), 'users.xlsx');
    }
}
