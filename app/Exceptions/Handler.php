<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        if ($exception->guards()[0] == 'admin' && is_array($exception->guards())) {
            return redirect()->guest('/login/admin');
        }
        if ($exception->guards()[0] == 'user' && is_array($exception->guards())) {
            return redirect()->guest('/');
        }
        if ($exception->guards()[0] == 'customersupport' && is_array($exception->guards())) {
            return redirect()->guest('/login/customersupport');
        }
        return redirect()->guest(route('home'));
    }
}
