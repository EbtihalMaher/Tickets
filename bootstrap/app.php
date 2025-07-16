<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(function () {
        // web routes
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // default api routes
        Route::middleware('api')
            ->prefix('api')
            ->group(base_path('routes/api.php'));

        // api v1
        Route::middleware('api')
            ->prefix('api/v1')
            ->group(base_path('routes/api_v1.php'));
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->is('api/*') || $request->expectsJson();
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            $errors = collect($e->errors())->flatMap(function ($messages, $field) {
                return collect($messages)->map(function ($msg) use ($field) {
                    return ['field' => $field, 'message' => $msg];
                });
            })->values();

            return response()->json([
                'status' => 422,
                'message' => 'Validation failed.',
                'errors' => $errors
            ], 422);
        });

        $exceptions->render(function (ModelNotFoundException|NotFoundHttpException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Resource not found'
            ], 404);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated'
            ], 401);
        });

        $exceptions->render(function (AuthorizationException $e) {
            return response()->json([
                'status' => 403,
                'message' => 'Forbidden'
            ], 403);
        });

        $exceptions->render(function (Throwable $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Server Error',
                'error_type' => class_basename($e),
            ], 500);
        });
    })->create();
