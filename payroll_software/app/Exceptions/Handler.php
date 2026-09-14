<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;

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
        // For web requests (page reloads, normal navigation)
        if ($request->expectsJson()) {
            // For AJAX requests
            return response()->json([
                'message' => 'Your session has expired. Please log in again.',
                'redirect' => route('login')
            ], 401);
        }

        // For regular page requests - redirect to login with message
        return redirect()->guest(route('login'))->with('error', 'Your session has expired. Please log in again.');
    }
}
