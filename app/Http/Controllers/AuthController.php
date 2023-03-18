<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.api', ['exept' => ['login', 'cretaeUser']]);
    }
    public function cretaeUser(Request $request)
    {
        $request->validate(
            [
                'name'     => 'required|string|max:20',
                'email'    => 'required|email',
                'password' => 'required|string|min:8|mixed|numbers|symbols'
            ]
        );
        // Create a new user with the submitted data
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password
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
   
}
