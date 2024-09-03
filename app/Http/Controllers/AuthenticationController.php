<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Actions\Passport\PasswordValidationRules;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Hash;


class AuthenticationController extends Controller
{
    //
    use PasswordValidationRules;

    public function register(Request $request){

        $validation = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => $this->passwordRules(),
            'password_confirmation' => 'required'
        ]);

        if ($validation->fails()) {
            
            return response()->json($validation->messages(), 422);

        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => Role::where('name', 'user')->first()->id
        ]);

        // create token for user
        $token = $user->createToken('auth_token')->accessToken;

        return response([
            'user' => [
                'number' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name,
                'profile' => $user->profile,
                'token' => $token
            ] 
        ]);

    }

    public function login(Request $request){

        $validation = Validator::make($request->all(), [
            'email' => [
                'required','exists:users,email'
            ],
            'password' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->messages(), 422);

        }

        $user = User::where('email', $request->email)->first();
            
        if (!$user || !Hash::check($request->password, $user->password)) {

            return response()->json([
                'email' => [ 0 => ''],
                'password' => [0 => 'The password is incorrect'],
            ], 422);

        } else{

                // create token for user
                $token = $user->createToken('auth_token')->accessToken;

                return response([
                    'user' => [
                        'number' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => $user->role->name,
                        'profile' => $user->profile,
                        'token' => $token
                    ], 
                    'baseUrl' => url('/')
                ]);
    
            }
        
    }

    public function logout(Request $request){
        // dd($request);
        Auth()->user()->token()->revoke();

        return response([
            'message' => 'Logged out succesfully'
        ]);

    }

}
