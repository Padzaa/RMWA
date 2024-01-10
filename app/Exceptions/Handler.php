<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Throwable;
use Inertia\Inertia;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

    }

    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e) && $e->getStatusCode() == 404) {
            return redirect()->route('welcome');
        }

        if ($e instanceof PostTooLargeException) {
            if (\Illuminate\Support\Facades\Request::header('X-Inertia')) {
                return Inertia::location('/message?error=Message%20not%20sent.');
            }
        }
        return parent::render($request, $e);
    }
}
