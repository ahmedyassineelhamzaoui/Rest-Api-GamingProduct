<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="create a new user",
     *     description="create a new user using the reqiuerd information name,email,password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         description="User information",
     *         required=true,
     *         @OA\JsonContent(
     *            @OA\Property(
     *               property="name",
     *               type="string",
     *               description="the guest name",
     *            ),
     *            @OA\Property(
     *               property="email",
     *               type="string",
     *               description="the guest email address",
     *            ),
     *            @OA\Property(
     *               property="password",
     *               type="string",
     *               description="the guest password",
     *            ),
     *         ),
     *     ),
     *     @OA\Response(
     *              response=200,
     *              description="account created successfuly",
     *              @OA\JsonContent(
     *                  @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     description="the response status",
     *                     example="success",
     *                  ),
     *                  @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="the message of the response",
     *                     example="account has benn created successfuly",
     *                  ),
     *                  @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     description="the information of the user who create her account",
     *                  ),
     *                  @OA\Property(
     *                     property="Authorization",
     *                     type="string",
     *                     description="the token of the new user"
     *                  ),
     *                  
     *              ),
     *     ),
     *)
     */

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
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate a user",
     *     description="Authenticate a user with their email and password",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         description="User credentials",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 description="The user's email address",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="The user's password",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The status of the response",
     *                 example="success",
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="The authenticated user object",
     *             ),
     *             @OA\Property(
     *                 property="Authorization",
     *                 type="object",
     *                 description="The authorization token",
     *                 @OA\Property(
     *                     property="token",
     *                     type="string",
     *                     description="The authorization token value",
     *                 ),
     *                 @OA\Property(
     *                     property="type",
     *                     type="string",
     *                     description="The authorization token type",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="the given data is invalid",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="A message describing the validation error",
     *                 example="The given data was invalid.",
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="An object containing validation error messages",
     *             ),
     *         ),
     *     ),
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8|string'
        ]);
        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid email or password'
            ], 401);
        }
        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user'  => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }
    /**
     * @OA\Get(
     *    path="/api/logout",
     *    summary="logout method",
     *    description="logout the session of the curent conect user",
     *    tags={"Authentication"},
     *    @OA\Response(
     *         response=200,
     *         description="successfuly log out",
     *         @OA\JsonContent(
     *            @OA\Property(
     *               property="status",
     *               type="string",
     *               description="the status of the response",
     *               example="the status of the response"
     *            ),
     *            @OA\Property(
     *               property="success",
     *               type="string",
     *               description="logout successsfuly",
     *               example="logout successsfuly"
     *            ),
     *         ),
     *    ),
     * )
     */
    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'log out Successfuly'  
        ]);
    }
}
