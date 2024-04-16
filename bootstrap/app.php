<?php

use App\Jobs\ScheduledTransaction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new ScheduledTransaction)->everyThirtySeconds()->onSuccess(function () {
            Log::info('Scheduled job started successfully');
        })->onFailure(function() {
            Log::error('Scheduled job interrupted');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $route = Route::current();
                
                Log::info("Model not found on API route {$route->getName()}", ['exception' => $e]);

                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.'
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {

                Log::info('Access denied for this user', ['user_id'=> auth()->id(), 'exception' => $e]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], $e->getStatusCode());
            }
        });

        // $exceptions->render(function (Exception $e, Request $request) {
        //     if ($request->is('api/*')) {

        //         Log::info('Internal exception happened', ['exception' => $e]);
                
        //         return response()->json([
        //             'success' => false,
        //             'message' => $e->getMessage()
        //         ], Response::HTTP_INTERNAL_SERVER_ERROR);
        //     }
        // });
    })->create();
