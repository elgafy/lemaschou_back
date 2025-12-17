<?php

use App\Http\Middleware\VerifyIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Artisan;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    //    $middleware->append([VerifyIsAdmin::class]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e,Request $request) {
            if ($request->is('api/*')) {
            if ($e instanceof AuthenticationException) {
                return response()->res(failed(), 'Session_Expired',[],404);
            }
            if ($e instanceof MethodNotAllowedHttpException) {
                return response()->res(failed(), 'method',[],405);
            }
            if ($e instanceof NotFoundHttpException) {
                return response()->res(failed(), 'url',[],404);
            }
            if ($e instanceof ThrottleRequestsException) {
                Artisan::call('cache:clear');
            }
        }
        });
    })->create();
