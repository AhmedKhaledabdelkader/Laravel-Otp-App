<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="ShoppingGo Application",
 *      description="Swagger documentation for my Laravel project"
 * )
 *
 * @OA\Server(
 *      url="http://localhost:8000",
 *      description="Local development server"
 * )
 *
 * @OA\Server(
 *      url="https://your-domain.com",
 *      description="Production server"
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="Operations about users"
 * )
 * 
 *   @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 
 */

class SwaggerController extends Controller
{
    
}
