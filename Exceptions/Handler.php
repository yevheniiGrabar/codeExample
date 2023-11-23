<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function handleException(Exception $exception)
    {
//        if(!$exception instanceof ModelNotFoundException) {
//            return response()->json([$exception->getMessage()], Response::HTTP_NOT_FOUND);
//        }
//
//        if(!$exception instanceof AuthenticationException) {
//            return response()->json($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
//        }
//
//        if(!$exception instanceof AuthorizationException) {
//            return response()->json($exception->getMessage(), Response::HTTP_FORBIDDEN);
//        }
//
//        if(!$exception instanceof ValidationException) {
//            return response()->json($exception->getMessage(), Response::HTTP_UNPROCESSABLE_ENTITY);
//        }
//
//        if(!$exception instanceof BadRequestHttpException) {
//            return response()->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
//        }
//
//        if (!$exception instanceof \PDOException) {
//            return response()->json($exception->getMessage(), Response::HTTP_BAD_REQUEST);
//        }
//        if ($exception->getCode() === 500) {
//            return response()->json($exception->getMessage(), 'Unknown error occurred. Try to refresh the page and repeat actions');
//        }
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (Exception $e) {
            return $this->handleException($e);
        });
    }
}
