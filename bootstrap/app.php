<?php

use App\Jobs\ProcessMonthlySaving;
use App\Jobs\ScheduledTransaction;
use App\Jobs\SendWeeklyAndMonthlyEmail;
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
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->job(new ScheduledTransaction)->monthly()->onSuccess(function () {
            Log::info('Scheduled transactions job started successfully');
        })->onFailure(function () {
            Log::error('Scheduled transactions job interrupted');
        });

        $schedule->job(new SendWeeklyAndMonthlyEmail)->monthly()->onSuccess(function () {
            Log::info('Scheduled job for sending weekly email successfully');
        })->onFailure(function () {
            Log::error('Scheduled job for sending weekly email interrupted');
        });

        $schedule->job(new SendWeeklyAndMonthlyEmail)->monthly()->onSuccess(function () {
            Log::info('Scheduled job for sending monthly email successfully');
        })->onFailure(function () {
            Log::error('Scheduled job for sending monthly email interrupted');
        });

        $schedule->job(new ProcessMonthlySaving)->everyThirtySeconds()->onSuccess(function () {
            Log::info('Scheduled job for processing monthly savings successfully');
        })->onFailure(function () {
            Log::error('Scheduled job for processing monthly savings interrupted');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                $route = Route::current();

                Log::info("Model not found on API route {$route->getName()}", ['exception' => $e]);

                return response()->json([
                    'success' => false,
                    'message' => 'Record not found.',
                ], Response::HTTP_NOT_FOUND);
            }
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {

                Log::info('Access denied for this user', ['user_id' => auth()->id(), 'exception' => $e]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*')) {

                Log::info('Access denied for this user', ['user_id' => auth()->id(), 'exception' => $e]);

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
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
