<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Actions\Passport\PasswordValidationRules;
use Hash;

class UpdateProfileInformation extends Controller
{
    //
    use PasswordValidationRules;

    public function update(Request $request){

        // update name and image if exists
        if (isset($request->name)) {
            
            $validation = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
    
            ]);
    
            if ($validation->fails()) {
                
                return response()->json($validation->messages(), 422);
    
            }

            $profile = $request->user()->profile ? $request->user()->profile : null;

            if ($request->file('profile') != null) {
                
                if ($request->user()->profile) {
                    
                    unlink(public_path('uploads') . '\\' . $request->user()->profile);

                }

                $file = $request->file('profile');
                $profile = $request->user()->id . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $profile);

            }

            $request->user()->update([
                'name' => $request->name,
                'profile' => $profile
            ]);
        }

        $user = $request->user();

        // update email if exists
        if (isset($request->email)) {

            $validation = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    'unique:users,email,'.$request->user()->id,
                ],
                'password' => 'required'
            ]);
    
            if ($validation->fails()) {
                
                return response()->json($validation->messages(), 422);
    
            }

            if (isset($user) && Hash::check($request->password, $user->password)) {
                
                $user->update([
                    'email' => $request->email,
                ]);

            }else{

                return response()->json([
                    'password' => 'The password is incorrect'
                ], 422);

            }
            
        }

        // return data
        if (isset($request->email) || isset($request->name)) {
    
            return response()->json([
                'number' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
                'profile' => $user->profile,
                'token' => Auth()->user()->token()->id
            ]);

        }
    }

    // update user password
    public function updateUserPassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ]);

        if ($validation->fails()) {
            
            return response()->json($validation->messages(), 422);

        }

        if (!Hash::check($request->current_password, $request->user()->password) ) {
            return response()->json([
                'current_password' => 'The Password is incorrect'
            ], 422);
        }

        $request->user()->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return response()->json(200);
    }

}
