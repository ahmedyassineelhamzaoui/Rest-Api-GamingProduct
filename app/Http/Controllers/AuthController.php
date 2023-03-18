<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function register(Request $request)
    {
      $request->validate([
          'name'      => 'required|max:30|string',
          'email'     => 'required|email|max:255',
          'password'  => 'required|string|min:8'
      ]);
      $user = User::create([
          'name'     => $request->name,
          'email'    => $request->email,
          'password' => Hash::make($request->password)
      ]);
      // generate a token for the user
      $token = Auth::login($user);
      return response()->json([
          'status' => 'success',
          'message' => 'User created successfully',
          'user' => $user,
          'authorisation' => [
              'token' => $token,
              'type' => 'bearer',
          ]
      ]);
    }
}
