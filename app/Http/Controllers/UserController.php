<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Mail\SendMailreset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;



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
        if(!$this->validateEmail($request->email)) {
            return $this->failedResponse();
        }
        $name=User::where('email',$request->email)->first()->name;
        $this->send($request->email,$name);
        return $this->successResponse();
    }

    public function send($email,$name)
    {
        $user=User::where('email',$email)->first();
        $token = $this->createToken($email);
        $user->remembre_token=$token;
        $user->save();
        Mail::to($email)->send(new SendMailreset($token, $email,$name));
    }

    public function createToken($email)
    {
        $token = Str::random(40);
        return $token;
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

    public function successResponse()
    {
        return response()->json([
            'data' => "Reset email link sent successfully, please check your inbox"
        ], 200);
    }

    
}
