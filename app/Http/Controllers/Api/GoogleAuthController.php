<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{

protected $userRepo ;


public function __construct(UserRepositoryInterface $userRepo)
{
    
    $this->userRepo=$userRepo ;

}



   /**
 * @OA\Get(
 *     path="/api/user/google/redirect",
 *     summary="Get Google OAuth Redirect URL",
 *     description="This endpoint returns the Google OAuth authorization URL that the frontend can use to redirect users for Google Sign-In.",
 *     operationId="redirectToGoogle",
 *     tags={"Authentication", "Google OAuth"},
 *     
 *     @OA\Response(
 *         response=200,
 *         description="Successfully generated Google OAuth redirect URL",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="success", description="Status of the request"),
 *             @OA\Property(property="url", type="string", example="https://accounts.google.com/o/oauth2/auth?client_id=YOUR_CLIENT_ID&redirect_uri=http://127.0.0.1:8000/api/auth/google/callback&scope=email+profile&response_type=code&state=random_string", description="Google OAuth authorization URL")
 *         )
 *     ),
 * 
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error (e.g. misconfigured Google credentials)",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="message", type="string", example="Could not generate Google redirect URL.")
 *         )
 *     )
 * )
 */

    public function redirectToGoogle()
    {
        $url = Socialite::driver('google')
            ->redirect()
            ->getTargetUrl();
        
        return response()->json([

            'status'=>'success',
            'url' => $url
        
        
        
        ]);
    }



 // Handle Google callback
 public function handleGoogleCallback()
 {
 
        
         $googleUser = Socialite::driver('google')->stateless()->user();
        
         $user = $this->userRepo->findByEmail($googleUser->getEmail()) ;
      
         $userWithUsername= $this->userRepo->findByUsername($googleUser->getName());
       
         if ($user || $userWithUsername) {
            
           return response()->json(

            [
                'message' => 'User already exists',
                'user'=>$user
            ]

            );
            
         }

    
         $user = $this->userRepo->create([
            'username' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'password' => Hash::make(Str::random(16)), 
            'is_verified' => true, 
        ]);
       


         $token = $user->createToken('api_token')->plainTextToken;

        
         return response()->json([
             'message' => 'Login successful',
             'token' => $token,
         ]);

 }










    
}
