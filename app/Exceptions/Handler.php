<?php

namespace App\Exceptions;

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
     * @param Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function register()
    {
         $this->renderable(function(Throwable $e, $request) {
             return $this->handleException($request, $e);
         });
    }

     public function handleException($request, Throwable $exception)
    {
     if($exception instanceof RouteNotFoundException) {
        return response('The specified URL cannot be  found.', 404);
     }
    }
}
