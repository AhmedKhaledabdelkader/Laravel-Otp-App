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

public function logout(Request $request)
{
    $token = $request->attributes->get('accessToken');
    $token->delete();
    return response()->json(['message' => 'Logged out from the current device'], 200);
    

  
}






public function logoutAll(Request $request)
{


    $request->user()->tokens()->delete();
    return response()->json(['message' => 'Logged out from all devices'], 200);
}




/*................Handling Forget Password.......................*/


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










    

