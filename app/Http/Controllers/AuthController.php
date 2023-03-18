<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rule;
class AuthController extends Controller
{
   
    public function createUser(Request $request)
    {
        $request->validate(
            [
                'name'     => 'required|string|max:20',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->where(function ($query) use ($request) {
                        return $query->where('email', $request->email);
                    })
                ],
                'password' => 'required|string|min:8',
            ]
        );
        // Create a new user with the submitted data
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password)
        ]);
        // generates a new JWT token
        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'user has been created successfuly',
            'user'    => $user,
            'authorization' => [
                'token' => $token,
                'type'  => 'bearer'
            ]
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
           'email'    => 'required|email',
           'password' => 'required|min:8|string'
        ]);
        $credentials = $request->only('email','password');
        $token = Auth::attempt($credentials);
        if(!$token){
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ],401);
        }
        $user= Auth::user();
        return response()->json([
            'status' => 'success',
            'user'  => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
            ]);
    }
}
