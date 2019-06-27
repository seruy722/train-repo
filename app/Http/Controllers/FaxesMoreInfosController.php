<?php

namespace App\Http\Controllers;

use App\Category;
use App\Exports\Fax\FaxesDataExport;
use App\FaxPriceForCategory;
use App\File;
use App\Price;
use App\PriceForTransporter;
use Illuminate\Http\Request;
use App\Imports\FaxesImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\FaxesMoreInfos;
use App\Fax;
use App\User;
use App\Traits\CleanData;
use App\Traits\FormatDate;

class FaxesMoreInfosController extends Controller
{
    use CleanData, FormatDate;

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
//        $FormatDatesClass = new FormatDatesClass('d-m-y');
        $file = $request->file('uploadedFile');

        $cleanedData = $this->clean((array)$request->all());

        // Сохранение файла на сервере
        if ($request->hasFile('uploadedFile')) {
            $fileExtension = $file->getClientOriginalExtension();
//            $fileName = $request->faxName . '.' . $fileExtension;
            $fileName = 'Fax' . $this->formatDateForSaveFile() . $fileExtension;
            $fileHash = 'факс ' . date("Y-m-d") . '_' . microtime(true);
            $fileUrl = 'faxes/' . $fileHash;

            $savedFileData = File::create([
                'file_name' => $request->faxName,
                'file_hash_name' => $fileHash,
                'file_size' => $file->getSize(),
                'file_ext' => $fileExtension,
                'url' => $fileUrl,
            ]);


            if (Storage::disk('local')->exists($fileUrl)) {
                return response()->json(['status' => false, 'fileName' => $fileName, 'pathToFile' => Storage::url($fileHash), 'error' => 'Файл с таким именем уже существует.']);
            } else {
                Storage::putFileAs('faxes', $file, $fileHash);
            }
        }
//        return response()->json(['status' => true, 'elem' => $cleanedData['transporterID']]);
        // Добавление факса
        $arrForCreateFax = ['transporter' => (int)$cleanedData['transporterID'], 'file_id' => $savedFileData->id, 'fax_name' => $request->faxName, 'date_departure' => $this->formatDateToMySqlDate($cleanedData['dateOfDeparture']), 'transport' => !!$cleanedData['transport']];
        $fax = Fax::create($arrForCreateFax);

        // Запись цен по категория за факс
        $transporterPrice = PriceForTransporter::where('transporter_id', (int)$cleanedData['transporterID'])->get();
        if ($transporterPrice->isNotEmpty()) {
            foreach ($transporterPrice->toArray() as $item) {
                FaxPriceForCategory::create(['fax_id' => $fax->id, 'category_id' => $item['category_id'], 'category_price' => $item['for_kg']]);
            }
        }

        // Загрузка данных файла на сервер
        $ImportedFaxArray = Excel::toArray(new FaxesImport, $file);
//        return response()->json(['status' => true, 'elem'=>$ImportedFaxArray]);
        foreach ($ImportedFaxArray as $item) {
            foreach ($item as $key => $elem) {
//                return response()->json(['status' => true, 'elem'=>$elem]);
                $cleanedElem = $this->clean($elem);
                // Пропускаем первые 2 строки
                if ($key === 0 || $key === 1) {
                    continue;
                }
                // Если все поля пустые выходим из итерации
                if (!$cleanedElem[0] && !$cleanedElem[1] && !$cleanedElem[2] && !$cleanedElem[3] && !$cleanedElem[4] && !$cleanedElem[5] && !$cleanedElem[6] && !$cleanedElem[7] && !$cleanedElem[8]) {
                    break;
                }
                // Если первые 5 полей пустые - то дописываем к предыдущей записи товары
                if (!$cleanedElem[0] && !$cleanedElem[1] && !$cleanedElem[2] && !$cleanedElem[3] && !$cleanedElem[4]) {
                    $lastEntry = FaxesMoreInfos::orderBy('id', 'desc')->first();
                    $things = $lastEntry->list_things;
//                    $things->{$cleanedElem[5]} = $cleanedElem[6];
                    $lastEntry->list_things = $things . ',' . $cleanedElem[5] . ':' . $cleanedElem[6];
                    $lastEntry->save();
                } else {
                    // Обрезаем из имени клиента приставку 007/
                    $startPos = stripos($cleanedElem[1], '007/');
                    $userName = substr($cleanedElem[1], $startPos + 4);
                    if (!is_numeric($startPos)) {
                        $userName = $cleanedElem[1];
                    }

                    $client = User::firstOrCreate(['name' => $userName], ['password' => 'default']);
//                    return response()->json(['status' => true, 'elem'=>$prices]);
//                    $category = Price::where('category_name', $client->id)->first();

                    // Категория
                    $category = Category::firstOrCreate(['category_name' => $cleanedElem[8]]);
                    $brandInNotation = stripos($cleanedElem[7], 'Бренд') !== false  || stripos($cleanedElem[7], 'Авиа') !== false;
                    $brandInCategory = stripos($cleanedElem[8], 'Бренд') !== false  || stripos($cleanedElem[8], 'Авиа') !== false;
                    $brand = $brandInNotation || $brandInCategory;

                    $prices = Price::where('client_id', $client->id)->where('category_id', $category->id)->first();

                    if ($prices) {
                        $forKg = $prices->for_kg;
                        $forPlace = $prices->for_place;
                    } else {
                        $forKg = 0;
                        $forPlace = 0;
                    }

                    $listThings = null;

                    if ($cleanedElem[5]) {
                        $listThings = $cleanedElem[5] . ':' . $cleanedElem[6];
                    }

//                    return response()->json(['status' => false, 'elem7'=> stripos($cleanedElem[8], 'Бренд') !== false]);

                    FaxesMoreInfos::create([
                        'code' => (string)$cleanedElem[0],
                        'place' => (int)$cleanedElem[2],
                        'kg' => (float)$cleanedElem[3],
                        'client_id' => (int)$client->id,
                        'fax_id' => (int)$fax->id,
                        'brand' => $brand,
                        'shop' => (string)$cleanedElem[4],
                        'for_kg' => (float)$forKg,
                        'for_place' => (float)$forPlace,
                        'list_things' => $listThings,
                        'notation' => (string)$cleanedElem[7],
                        'category_id' => (int)$category->id,
                    ]);
                }
            }
        }

        // Данные факса с расширением
        $file = File::where('id', $fax->file_id)->first();
        $fax->file_ext = $file->file_ext;
//        $fullFaxInfo = FaxesMoreInfos::where('fax_id', $fax->id)->get();
        return response()->json(['status' => true, 'fax' => $fax, 'groupedData' => $this->sampledData($fax->id)]);
    }


    public function updateFaxData(Request $request)
    {
//        return response()->json(['status' => false, 'ARR'=>$request->all()]);
        $this->validate($request, [
            '*.code' => 'max:255',
            '*.client_id' => 'required|numeric',
            '*.category_id' => 'required|numeric',
            '*.fax_id' => 'required|numeric',
            '*.place' => 'required|numeric',
            '*.kg' => 'required|numeric',
            '*.shop' => 'max:255',
            '*.brand' => 'required|boolean',
            '*.for_kg' => 'required|numeric',
            '*.for_place' => 'required|numeric',
            '*.notation' => 'max:255',
        ]);

        $data = $request->all();
        $user = $request->user();
        $faxID = $data[0]['fax_id'];

//        return response()->json(['status' => false, 'ARR'=>$data]);
        foreach ($data as $value) {
            $arrvalue = (array)$value;
//            $client = User::where('id', $arrvalue['client_id'])->where('name', $arrvalue['name'])->first();
            $client = User::firstOrCreate(['name' => $arrvalue['name'], 'id' => $arrvalue['client_id']], ['password' => 'default']);
            $category = Category::firstOrCreate(['category_name' => $arrvalue['category_name'], 'id' => $arrvalue['category_id']]);
            $clientID = $client->id;

            $prices = Price::firstOrCreate(['client_id' => $clientID, 'category_id' => $category->id], ['category_id' => $category->id, 'user_id' => $user->id]);
            $prices->category_id = $category->id;
//            return response()->json(['status' => false, 'ARR'=>$prices]);
            $brand = (boolean)$arrvalue['brand'];

            if ($arrvalue['for_kg'] && !$prices->for_kg) {
                $prices->for_kg = (float)$arrvalue['for_kg'];
            }

            if ($arrvalue['for_place'] && !$prices->for_place) {
                $prices->for_place = (float)$arrvalue['for_place'];
            }

            $changePrices = $arrvalue['changeValues'];

            if ($changePrices['for_kg']) {
                $prices->for_kg = (float)$arrvalue['for_kg'];
            }

            if ($changePrices['for_place']) {
                $prices->for_place = (float)$arrvalue['for_place'];
            }


            $prices->save();
//            return response()->json(['status' => true, 'groupedData' => [], 'dfsdf'=>$prices]);

            FaxesMoreInfos::where('id', $arrvalue['id'])->update([
                'code' => (string)$arrvalue['code'],
                'place' => (int)$arrvalue['place'],
                'kg' => (float)$arrvalue['kg'],
                'client_id' => $clientID,
                'category_id' => $category->id,
                'fax_id' => (int)$arrvalue['fax_id'],
                'brand' => $brand,
                'shop' => (string)$arrvalue['shop'],
                'for_kg' => (float)$arrvalue['for_kg'],
                'for_place' => (float)$arrvalue['for_place'],
                'list_things' => $arrvalue['list_things'],
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

    public function export(Request $request)
    {
//        $res = new FaxExport($request->faxData, $request->only('headers'), $request->faxID, $request->categories);
//        return response()->json(['status' => $res->check()]);
        return Excel::download(new FaxesDataExport($request->faxData, $request->only('headers'), $request->faxID, $request->categories), 'users.xlsx');
    }

//    public function export(Request $request)
//    {
////        $res = new FaxExport($request->faxData, $request->only('headers'), $request->faxID, $request->categories);
////        return response()->json(['status' => $res->check()]);
//        return Excel::download(new RssExport($request->faxData), 'users.xlsx');
//    }
}
