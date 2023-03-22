<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMailreset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    /**
     * @OA\Post(
     *    path="/api/reset-pasword",
     *    summary="forgot passwword",
     *    description="method that allows user who has an account to reset his password",
     *    tags={"User"},
     *    @OA\RequestBody(
     *       description="User email",
     *       required=true,
     *       @OA\JsonContent(
     *          @OA\Property(
     *             property="email",
     *             type="string",
     *             description="email of user",
     *             example="enter your email",
     *          ),
     *       ),
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="email reset was send successfuly",
     *       @OA\JsonContent(
     *       @OA\Property(
     *          property="status",
     *          description="the status of the response",
     *          example="the staus of the response",
     *       ),
     *       @OA\Property(
     *          property="message",
     *          type="object",
     *          description="token that need to reset your password",
     *          @OA\Property(
     *             property="token",
     *             type="string",
     *             description="the token of user",
     *             example="the token of user",
     *          ),
     *       ),
     *       ),
     *    ),
     *    @OA\Response(
     *       response=404,
     *       description="the email dosn't exist",
     *       @OA\JsonContent(
     *       @OA\Property(
     *           property="status",
     *           type="string",
     *           description="status of response",
     *           example="error"
     *       ),
     *       @OA\Property(
     *           property="message",
     *           type="string",
     *           description="the email dosn't exist in database",
     *           example="the email dosn't exist"
     *       ),
     *       ),
     *    ),
     * )
     */
    public function sendEmail(Request $request)
    {
        if (!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }
        $user = User::where('email', $request->email)->first();
        $token = Str::random(40);
        $user->remembre_token = $token;
        $user->save();
        Mail::to($request->email)->send(new SendMailreset($token, $request->email, $user->name));
        $this->send($request->email, $user->name);
        return $this->successResponse($token);
    }

    public function send($email, $name)
    {
        $user = User::where('email', $email)->first();
        
    }
    public function validateEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function failedResponse()
    {
        return response()->json([
            'error' => "Email was not found in the Database"
        ], 404);
    }

    public function successResponse($token)
    {
        return response()->json([
            'message' => "Reset email link sent successfully, please check your inbox",
            'token'  => $token
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = $request->user();
        if ($request->isMethod('post')) {
            $request->validate([
                'password' => 'required|min:8',
                'confirm_password' => 'required|min:8|same:password',
                'token' => 'required|string'
            ]);
            $user = User::where('remembre_token', $request->token)->first();
            if ($user) {
                $user->password = Hash::make($request->password);
                $user->save();
                return response()->json([
                    'statuts' => 'success',
                    'message' => 'your password has been updated successfuly',
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'you do not have permession to access into this page'
                ], 401);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'method not allowd'
            ], 405);
        }
    }
}
