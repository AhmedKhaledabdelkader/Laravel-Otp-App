<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Throwable;

class AuthController extends Controller
{


protected $userRepo ;

    public function __construct(UserRepositoryInterface $userRepo)
    {

        $this->userRepo = $userRepo ;
        
    }


/**
 * @OA\Post(
 *     path="/api/user/register",
 *     tags={"Auth"},
 *     summary="Register a new user with OTP verification",
 *     description="This endpoint registers a new user, hashes the password, and generates an OTP code.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"username","email","password"},
 *             @OA\Property(property="username", type="string", example="john_doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Ahmed@123"),
 *             @OA\Property(property="password_confirmation",type="string",format="password",example="Ahmed@123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User registered successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="username", type="string", example="john_doe"),
 *             @OA\Property(property="email", type="string", example="john@example.com"),
 *            @OA\Property(property="is_verified", type="boolean", example=false),
 *             @OA\Property(property="createdAt", type="string", format="date-time", example="2025-10-01T09:15:30Z"),
 *             @OA\Property(property="updatedAt", type="string", format="date-time", example="2025-10-01T09:20:45Z")
 *             
 *             
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *     response=500,
 *     description="server error"
 * )
 * )
 */

public function register(Request $request){


    $otp = rand(100000, 999999);


    $data=[

        'username'=>$request->username,
        'email'=>$request->email,
        'password' => Hash::make($request->password),
        'otp'=>$otp,
        'otp_expires_at' => now()->addMinutes(10),


    ];

    $user=$this->userRepo->create($data) ;


    return new UserResource($user);




}













/**
 * @OA\Post(
 *     path="/api/user/verify-otp",
 *     summary="Verify OTP",
 *     description="Verifies the OTP sent to the user's email to activate their account.",
 *     tags={"Auth"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","otp"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="otp", type="string", example="123456")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="OTP verified successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(property="message", type="string", example="user verified successfully")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired OTP",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid or expired OTP")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="user with this email not found")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */

public function verifyOtp(Request $request){



    $user=$this->userRepo->findByEmail($request->email);


    if(!$user){

        return response()->json([

            "status"=>"error",
            "message"=>"user with this email not found"

        ],404);

    }


    
    if ($user->otp != $request->otp || $user->otp_expires_at < now()) {

        
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

    $user->is_verified = true;
    $user->otp = null;
    $user->otp_type=null;
    $user->otp_expires_at = null;
    $user->save();


    return response()->json([

        "status"=>"success",
        "message"=>"user verified successfully"

    ],200);





}












/**
 * @OA\Post(
 *     path="/api/user/resend-otp",
 *     summary="Resend OTP",
 *     description="Resends a new OTP to the user's email if the user exists and is not already verified.",
 *     tags={"Auth"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", example="user@example.com")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="New OTP sent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="New OTP has been sent to your email.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Email already verified",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email is already verified")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="user with this email not found")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */

public function resendOtp(Request $request){



    $user=$this->userRepo->findByEmail($request->email);


    if(!$user){

        return response()->json([

            "status"=>"error",
            "message"=>"user with this email not found"

        ],404);

    }

    if ($user->is_verified) {
        return response()->json(['message' => 'Email is already verified'], 400);
    }


    // Generate new OTP
    $otp = rand(100000, 999999);
    $user->otp = $otp;
    $user->otp_type="resend";
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save();



    return response()->json(['message' => 'New OTP has been sent to your email.'], 200);




}





/**
 * @OA\Post(
 *     path="/api/user/login",
 *     tags={"Auth"},
 *     summary="Login user with email/username and password",
 *     description="This endpoint allows a user to log in using either their username or email with a password. Returns a token if successful.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             oneOf={
 *                 @OA\Schema(
 *                     required={"username","password"},
 *                     @OA\Property(property="username", type="string", example="john_doe"),
 *                     @OA\Property(property="password", type="string", format="password", example="secret123")
 *                 ),
 *                 @OA\Schema(
 *                     required={"email","password"},
 *                     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *                     @OA\Property(property="password", type="string", format="password", example="secret123")
 *                 )
 *             }
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Login successful",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="login to the system is successfully"),
 *             @OA\Property(property="token", type="string", example="1|abcdef1234567890")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Incorrect password",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="password is incorrect")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Email not verified",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="please verify your email first")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="user not found")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="username",
 *                     type="array",
 *                     @OA\Items(type="string", example="The username field is required.")
 *                 )
 *             )
 *         )
 *     )
 * )
 */


public function login(Request $request){


    $user=null ;

    if($request->has('username')){

        $user=$this->userRepo->findByUsername($request->username);

    }
    else{

        $user=$this->userRepo->findByEmail($request->email);

    }


    if ($user) {

        if (Hash::check($request->password, $user->password)) {

            if ($user->is_verified) {


                $token = $user->createToken('api_token')->plainTextToken;



                return response()->json([
                    'message'=>"login to the system is successfully",
                    'token' => $token
                ], 200);



                
            }

            else{


                return response()->json([

                    "status"=>"error",
                    "message"=>"please verify your email first"

                ],403);


            }
        }
        else{

            return response()->json([

                "status"=>"error",
                "message"=>"password is incorrect"

            ],401);

        }
    }
    else{

        return response()->json([

            "status"=>"error",
            "message"=>"user not found"

        ],404);


    }








}






/**
 * @OA\Post(
 *     path="/api/user/logout",
 *     summary="Logout user",
 *     description="Logs out the authenticated user from the current device by revoking the access token.",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Successfully logged out",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out from the current device")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - No valid token provided",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */


public function logout(Request $request)
{
    $token = $request->attributes->get('accessToken');
    $token->delete();
    return response()->json(['message' => 'Logged out from the current device'], 200);
    

  
}










/**
 * @OA\Post(
 *     path="/api/user/logoutAll",
 *     summary="Logout from all devices",
 *     description="Logs out the authenticated user from **all devices** by revoking all access tokens.",
 *     tags={"Auth"},
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Successfully logged out from all devices",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Logged out from all devices")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=401,
 *         description="Unauthorized - No valid token provided",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Unauthenticated")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */



public function logoutAll(Request $request)
{


    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out from all devices'], 200);
}




/*................Handling Forget Password.......................*/












/**
 * @OA\Post(
 *     path="/api/user/forget",
 *     summary="Forgot Password - Send OTP",
 *     description="Generates and sends an OTP to the user's email for password reset if the user exists.",
 *     tags={"Auth"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", example="user@example.com")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="OTP sent successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="OTP has been sent to your email.")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="user with this email not found")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */




public function forgetPassword(Request $request){



    $user=$this->userRepo->findByEmail($request->email);

    if(!$user){

        return response()->json([

            "status"=>"error",
            "message"=>"user with this email not found"

        ],404);

    }


    $otp = rand(100000, 999999);
    $user->otp = $otp;
    $user->otp_type="forget";
    $user->otp_expires_at = now()->addMinutes(10);
    $user->save();


    return response()->json(['message' => 'OTP has been sent to your email.'], 200);



}












/**
 * @OA\Post(
 *     path="/api/user/reset",
 *     summary="Reset Password",
 *     description="Resets the user's password using a valid OTP.",
 *     tags={"Auth"},
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email", "otp", "password", "password_confirmation"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="otp", type="integer", example=123456),
 *             @OA\Property(property="password", type="string", format="password", example="newStrongPassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", example="newStrongPassword123")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=200,
 *         description="Password reset successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Password has been reset successfully")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=400,
 *         description="Invalid or expired OTP",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Invalid or expired OTP")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="User not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User not found")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(
 *                 property="errors",
 *                 type="object",
 *                 @OA\Property(
 *                     property="password",
 *                     type="array",
 *                     @OA\Items(type="string", example="The password confirmation does not match.")
 *                 ),
 *                 @OA\Property(
 *                     property="password_confirmation",
 *                     type="array",
 *                     @OA\Items(type="string", example="The password confirmation field is required.")
 *                 )
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Internal Server Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Something went wrong on the server")
 *         )
 *     )
 * )
 */



public function resetPassword(Request $request){


    $user=$this->userRepo->findByEmail($request->email);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->otp != $request->otp || $user->otp_expires_at < now()) {
        return response()->json(['message' => 'Invalid or expired OTP'], 400);
    }

     // Update password
     $user->password = Hash::make($request->password);
     $user->otp = null;
     $user->otp_type=null ;
     $user->otp_expires_at = null;
     $user->save();


    return response()->json(['message' => 'Password has been reset successfully'], 200);




}




























}










    

