<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\ApiException; // <-- Import your custom exception
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'validate.user'=> \App\Http\Middleware\ValidateUser::class,
            'validate.otp'=> \App\Http\Middleware\ValidateOtp::class,
            'validate.resend'=> \App\Http\Middleware\ValidateResendOtp::class,
            'validate.auth'=> \App\Http\Middleware\ValidateUserLogin::class,
            'validate.reset'=> \App\Http\Middleware\ValidateResetPassword::class,
            'auth.user'=> \App\Http\Middleware\AuthenticationMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        


        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Route not found'
            ], 404);
        });




     
        $exceptions->render(function (Throwable $e, $request) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        });


      




    })
    ->create();
