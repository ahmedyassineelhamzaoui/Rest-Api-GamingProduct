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
    public function closeAccount(Request $request)
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
    /**
     * @OA\Put(
     *     path="/api/updateProfile",
     *     summary="Update user information",
     *     description="Update the authenticated user's information",
     *     tags={"User"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         description="The updated user information",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 description="The user's new name",
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 description="The user's new email address",
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 description="The user's new password",
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
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
     *                 example="User updated successfully",
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="The updated user object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     description="The user's ID",
     *                     example=1,
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="The user's name",
     *                     example="John Doe",
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     description="The user's email address",
     *                     example="example@example.com",
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized action",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
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
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => $user,
        ]);
    }
    /**
     * @OA\Get(
     *    path="/api/users",
     *    summary="all users",
     *    description="method to return all users",
     *    tags={"User"},
     *    @OA\Response(
     *        response=200,
     *        description="the response of success ",
     *        @OA\JsonContent(
     *            @OA\Property(
     *                property="status",
     *                type="string",
     *                description="the response of your request",
     *                example="success",
     *            ),
     *            @OA\Property(
     *                property="users",
     *                type="object",
     *                description="all users",
     *                @OA\Property(
     *                    property="id",
     *                    type="number",
     *                    description="id of user",
     *                    example=1,
     *                ),
     *                @OA\Property(
     *                    property="name",
     *                    type="string",
     *                    description="the user name",
     *                    example="mohamed",
     *                ),
     *                @OA\Property(
     *                    property="email",
     *                    type="string",
     *                    description="the user email",
     *                    example="mohamed@gmail.com",
     *                ),
     *            ),
     *        ),
     *   
     *    )
     * 
     * )
     */
    public function getAllUsers()
    {
        $user=user::all(['id', 'name', 'email']);
        $numberUsers=$user->count();
        if($numberUsers>0){
            return response()->json([
                'message' => 'users',
                'users' => $user
            ]);
        }
        else{
            return response()->json([
                'message' => 'no data available',
            ]);
        }
    }
}
