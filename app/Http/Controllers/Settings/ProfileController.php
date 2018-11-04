<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required||max:255|email|unique:users,email,' . $user->id,
        ]);

        return tap($user)->update($request->only('name', 'email'));
    }

    public function updateProfileFoto(Request $request)
    {
        if ($request->hasFile('profilephoto')) {
            $this->validate($request, [
                'profilephoto' => 'image'
            ]);
            $user = $request->user();
            $file = $request->file('profilephoto');
            $fileName = Storage::disk('images')->put('/profile', $file);

            if ($user->set_photo_url) {
                $oldFileName = substr($user->set_photo_url, strpos($user->set_photo_url, '/') + 1);
                $exists = Storage::disk('images')->exists('/profile/' . $oldFileName);

                if ($exists) {
                    Storage::disk('images')->delete('/profile/' . $oldFileName);
                }
            }

            $user->set_photo_url = $fileName;

            return tap($user)->save();
        }
    }
    public function cleanData($value)
    {
        $arr = array_map("trim", $value);
        $arr = array_map("strip_tags", $arr);
        $arr = array_map("stripcslashes", $arr);
        return $arr;
    }
}
