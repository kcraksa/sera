<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    // public function register()
    // {
    //     $this->reportable(function (Throwable $e) {
    //         if (app()->bound('sentry')) {
    //             app('sentry')->captureException($e);
    //         }
    //     });
    // }

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    // public function report(Throwable $exception)
    // {
    //     parent::report($exception);
    // }

    public function report(Throwable $exception)
    {
        if (app()->bound('sentry') && $this->shouldReport($exception)) {
            app('sentry')->captureException($exception);
        }

        parent::report($exception);
    }



    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        // start custom code
        // if($exception->getStatusCode() == 404){
        //     return response()->json([
        //         'status' => "error",
        //         'message' => "Page Not Found",
        //         'data' => ""
        //     ], 404);
        // }
        // if($exception->getStatusCode() == 500){
        //     return response()->json([
        //         'status' => "error",
        //         'message' => "Internal Server Error",
        //         'data' => ""
        //     ], 500);
        // }
        // end custom code

        return parent::render($request, $exception);
    }
}
