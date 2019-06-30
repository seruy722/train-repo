<?php

namespace App\Http\Controllers;

use App\Cargo;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\User;

class UserController extends Controller
{

    private $folderNameForSaveImage = '/users';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allUsers = User::orderBy('created_at', 'DESC')->get();
        return response()->json(['status' => true, 'users' => $allUsers]);
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        return response()->json(['status' => true,'item' => $request->all()]);
        $array = (array)$request->only('name', 'email', 'password', 'set_photo_url', 'role', 'code', 'sex', 'phone', 'city');
        if ($array['email'] !== 'seruy722@gmail.com' || $array['email'] !== 'pisyruk007@gmail.com') {
            return response()->json(['status' => false]);
        }
        $this->cleanData($array);

        $type = $request->type;
        $file = null;

        if ($request->hasFile('file')) {
            $this->validate($request, [
                'file' => 'image',
            ]);
            $file = $request->file('file');
            $fileName = Storage::disk('images')->put($this->folderNameForSaveImage, $file);
            // Сжатие изображений
//            $name = substr($fileName, strpos($fileName, '/') + 1);
//            $filePath = base_path('client/static/images/client/'.$name);
//            try {
//                \Tinify\setKey("zcGcy2cl3HKLQ4q2Ls4F2TxZ6FWKxGfg");
//                $source = \Tinify\fromFile($filePath);
//                $source->toFile($filePath);
//            } catch (\Tinify\AccountException $e) {
//
//            }
            $array['set_photo_url'] = $fileName;
        }

        if ($request->has('password')) {
            $array['password'] = bcrypt($array['password']);
        }

        switch ($type) {
            case 'save':
                $this->validate($request, [
                    'name' => 'required|string|unique:users|max:255',
                    'email' => 'required|email|max:255',
                    'password' => 'required|min:6|max:255',
                    'role' => 'required|max:255',
                    'code' => 'max:255',
                    'sex' => 'max:255',
                    'city' => 'max:255',
                    'phone' => 'max:255',
                ]);

                $user = User::create($array);
                return response()->json(['status' => true, 'type' => 'save', 'user' => $user]);
                break;

            case 'update':
                $itemID = $request->id;
                $this->validate($request, [
                    'name' => 'string|max:255|unique:users,name,' . $itemID,
                    'email' => 'email|max:255',
                    'password' => 'min:6|max:255',
                    'role' => 'max:255',
                    'code' => 'max:255',
                ]);

                $user = User::findOrFail($itemID);

                if ($file) {
                    if ($user->set_photo_url) {
                        $fileName = substr($user->set_photo_url, strpos($user->set_photo_url, '/') + 1);
                        $exists = Storage::disk('images')->exists($this->folderNameForSaveImage . '/' . $fileName);

                        if ($exists) {
                            Storage::disk('images')->delete($this->folderNameForSaveImage . '/' . $fileName);
                        }
                    }
                }

                User::where('id', $itemID)->update($array);

                $updatedUser = User::findOrFail($itemID);
                return response()->json(['status' => true, 'type' => 'update', 'user' => $updatedUser]);
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

    public function deleteFoto(Request $request)
    {
        $user = User::find($request->id);
        if ($user->set_photo_url) {
            $fileName = substr($user->set_photo_url, strpos($user->set_photo_url, '/') + 1);
            $exists = Storage::disk('images')->exists($this->folderNameForSaveImage . '/' . $fileName);

            if ($exists) {
                Storage::disk('images')->delete($this->folderNameForSaveImage . '/' . $fileName);
            }
            $user->set_photo_url = null;
            $user->save();
            return response()->json(['status' => true, 'user' => $user]);
        }
    }

    public function getClients()
    {
        $clientsNames = User::all(['name', 'id'])->toArray();

        return response()->json(['status' => true, 'clientsNames' => $clientsNames]);
    }

    public function getUsersClients()
    {
        $clients = User::where('role', 'user')->orderBy('name')->get(['id', 'name']);

        return response()->json(['status' => true, 'clients' => $clients]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $userID = $request->id;
        $user = User::findOrFail($userID);
        if ($user) {
            $user->delete();
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);
    }

    public function getUsersNumberForSendingMessages(Request $request)
    {
        $data = $request->toArray();
//        return response()->json(['status' => true, 'data' => $data]);
        $newArr = [];
        foreach ($data as $item) {
            $user = User::find($item['client_id']);
            $item['client_id'] = $user->phone;
            array_push($newArr, ['phone' => $user->phone, 'name' => $user->name, 'sum' => $item['sum']]);
//            foreach ($item as $key=> $elem) {
//                $user = User::find($elem['client_id']);
//                $elem['']
//                array_push($newArr, [$user->phone=>$elem]);
//            }

        }
        return response()->json(['status' => true, 'data' => $newArr]);
    }

    public function getFreeCodes(Request $request)
    {
        $to = range(0, $request->to);
        $clients = User::all(['name'])->toArray();
        $newArr = [];
        foreach ($clients as $client) {
            if (is_numeric($client['name'])) {
                array_push($newArr, (int)$client['name']);
            }
        }
        $arr = array_unique($newArr, SORT_NUMERIC);
        $arr = array_values($arr);
        $newCodes = [];

        foreach ($to as $item) {
            if (!in_array($item, $arr)) {
                array_push($newCodes, $item);
            }
        }
        sort($newArr, SORT_NUMERIC);

        return response()->json(['items' => $newCodes]);
    }

    public function addClient(Request $request)
    {
        $this->validate($request, [
            'clientCode' => 'required|max:50',
        ]);

        $clientCode = $request->clientCode;
        $client = User::where('name', $clientCode)->first();
//        User::destroy(689);
        if (!$client) {
            $newClient = User::create([
                'name' => $clientCode,
                'password' => 'default'
            ]);

            return response()->json(['client' => $newClient->name]);
        }

        return response()->json(['client' => null]);
    }
}
