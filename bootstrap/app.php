<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            // Requer TODAS as permissões listadas no token (condição lógica E)
            'abilities' => CheckAbilities::class,

            // Requer PELO MENOS UMA das permissões listadas no token (condição lógica OU)
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->stopIgnoring(HttpException::class);

        $exceptions->stopIgnoring(HttpException::class);

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ModelNotFoundException) {
                    $model = class_basename($e->getModel());
                    return response()->json([
                        'message' => "$model not found."
                    ], 404);
                }

                if ($e instanceof NotFoundHttpException) {
                    $previous = $e->getPrevious();
                    if ($previous instanceof ModelNotFoundException) {
                        $model = class_basename($previous->getModel());
                        return response()->json([
                            'message' => "$model not found."
                        ], 404);
                    }

                    return response()->json([
                        'message' => 'Resource not found.'
                    ], 404);
                }
            }

            return null; // fallback
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'This action is unauthorized.',
                ], 403);
            }
        });

        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*') || $request->expectsJson();
        });
    })->create();
