<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\FaxesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
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

        $faxData = DB::table('faxes_more_info')
            ->where('fax_id', $request->faxID)
            ->join('users', 'faxes_more_info.client_id', '=', 'users.id')
            ->join('prices', 'faxes_more_info.client_id', '=', 'prices.client_id')
            ->select('faxes_more_info.client_id', 'faxes_more_info.place','faxes_more_info.kg','faxes_more_info.brand','users.name', 'prices.for_kg', 'prices.for_place', 'prices.for_kg_brand', 'prices.for_place_brand', 'prices.for_commission')
            ->get();

        $groupedData = $faxData->groupBy('client_id');

        return response()->json(['status' => true, 'groupedData' => $groupedData]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'uploadedFile' => 'required|file|max:10240||mimes:xls,xlsx,csv',
            'faxName' => 'required|string|min:3|max:255',
            'dateOfDeparture' => 'required|date|max:255'
        ]);

        // Класс доп функций
        $FormatDatesClass = new FormatDatesClass('d-m-y');
        $file = $request->file('uploadedFile');

        $cleanedData = $this->clean((array)$request->all());

        // Сохранение файла на сервере
        if ($request->hasFile('uploadedFile')) {
            $fileExtention = $file->getClientOriginalExtension();
            $fileName = $request->faxName . '.' . $fileExtention;


            if (Storage::disk('local')->exists($fileName)) {
                return response()->json(['status' => false, 'fileName' => $fileName, 'pathToFile' => Storage::url($fileName), 'error' => 'Файл с таким именем уже существует.']);
            } else {
                Storage::putFileAs('faxes', $file, $fileName);
            }
        }

        // Добавление факса
        $arrForCreateFax = ['fax_name' => $cleanedData['faxName'], 'date_departure' => $FormatDatesClass->formatServerDate($cleanedData['dateOfDeparture'])];
        $fax = Fax::create($arrForCreateFax);

        $formatedDatesFax = $FormatDatesClass->formatDatesFields($fax->toArray(), ['created_at', 'date_departure']);

        // Загрузка данных файла на сервер
        $ImportedFaxArray = Excel::toArray(new FaxesImport, $file);
        foreach ($ImportedFaxArray as $item) {
            foreach ($item as $key => $elem) {
                // Пропускаем первые 2 строки
                if ($key === 0 || $key === 1) {
                    continue;
                }
                // Если все поля пустые выходим из итерации
                if (!$elem[0] && !$elem[1] && !$elem[2] && !$elem[3] && !$elem[4] && !$elem[5] && !$elem[6] && !$elem[7]) {
                    break;
                }
                // Если первый 5 полей пустые - то дописываем к предыдущей записи товары
                if (!$elem[0] && !$elem[1] && !$elem[2] && !$elem[3] && !$elem[4]) {
                    $lastEntry = FaxesMoreInfos::orderBy('id', 'desc')->first();
                    $things = json_decode($lastEntry->name_of_things);
                    $things->{$elem[5]} = $elem[6];
                    $lastEntry->name_of_things = json_encode($things);
                    $lastEntry->save();
                } else {
                    // Обрезаем из имени клиента приставку 007/
                    $startPos = stripos($elem[1], '007/');
                    $userName = substr($elem[1], $startPos + 4);
                    if (!is_numeric($startPos)) {
                        $userName = $elem[1];
                    }

                    $client = User::firstOrCreate(['name' => $userName], ['password' => 'default']);

                    try {
                        FaxesMoreInfos::create([
                            'code' => $elem[0],
                            'place' => $elem[2],
                            'kg' => $elem[3],
                            'client_id' => $client->id,
                            'fax_id' => $fax->id,
                            'brand' => $elem[7] === 'Бренд',
                            'shop' => $elem[4],
                            'name_of_things' => json_encode([$elem[5] => $elem[6]]),
                            'notation' => $elem[7]
                        ]);
                    } catch (\Exception $e) {
                        return response()->json(['status' => false, 'error' => 'Ошибка при записи данных.', 'row' => $elem, 'exception' => $e]);
                    }
                }
            }
        }
        $fullFaxInfo = FaxesMoreInfos::where('fax_id', $fax->id)->get();
        return response()->json(['status' => true, 'fax' => $formatedDatesFax, 'fullFaxInfo' => $fullFaxInfo]);
    }
}
