<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $token = $user->createToken('Laravel')->accessToken;

        $userData = [
            "email" => $user->email,
            "fullName" => $user->username,
            "username" => $user->username,
            "id" => $user->id,
        ];

        return response()->json(['accessToken' => $token, 'userData' => $userData],  200);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (auth()->attempt($request->only('email', 'password'))) {
            $token = auth()->user()->createToken('Laravel')->accessToken;
            $user = new User();
            $user = $user->where('email', $request->email)->first();
            $userData = [
                "email" => $user->email,
                "fullName" => $user->username,
                "username" => $user->username,
                "avatar" => $user->settings->avatar_link ?? '',
                "id" => $user->id,
            ];
            return response()->json(['accessToken' => $token, 'userData' => $userData],  200);
        }

        return response()->json(['error' => 'Unauthorised'], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }
}
