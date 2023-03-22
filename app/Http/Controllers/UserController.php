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
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['sendEmail', 'changePassword', 'failedResponse', 'successResponse']]);
    }
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
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->failedResponse();
        }
        $token = Str::random(40);
        $user->remembre_token = $token;
        $user->save();
        Mail::to($request->email)->send(new SendMailreset($token, $request->email, $user->name));
        return $this->successResponse($token);
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
    /**
     * @OA\POST(
     *     path="/api/changePassword",
     *     summary="change password ",
     *     description="change the password of the user",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *        required=true,
     *        description="user information to change his password",
     *        @OA\JsonContent(
     *            @OA\Property(
     *                property="pasword",
     *                type="string",
     *                description="the new password",
     *                example="enter your new password",
     *            ),
     *            @OA\Property(
     *                property="confirm password",
     *                type="string",
     *                description="the confirmation of password",
     *                example="confirm your password",
     *            ),
     *            @OA\Property(
     *                property="token",
     *                type="string",
     *                description="the token to reset the password ",
     *                example="enter the token that was in your email address"
     *            ),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="password changed successfuly",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The status of the response",
     *                 example="success",
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="A message describing the response status",
     *                 example="Password has been changed successfully",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=405,
     *         description="Method not allowd",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *               property="status",  
     *               type="string",
     *               description="the status response",
     *               example="error"
     *             ),
     *             @OA\Property(
     *               property="message",  
     *               type="string",
     *               description="An object containing validation error messages",
     *               example="validation error",
     *             ),
     *         ),
     *
     *        ),
     *     ),
     * )
     */
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
    /**
     * @OA\DELETE(
     *     path="/api/deleteProfile",
     *     summary="Delete the conected user",
     *     description="Delete the authenticated user",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="string",
     *                 description="The status of the response",
     *                 example="success",
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 description="A message describing the response status",
     *                 example="User deleted successfully",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized action",
     *     ),
     * )
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Profile deleted successfully',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'faild to complete this request',
            ], 401);
        }
    }
}
