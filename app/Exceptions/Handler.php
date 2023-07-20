<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Log;
use Throwable;
use StdClass;
use Exception;

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
    public function report(Throwable $exception)
    {
        // parent::report($exception);
        if(!$exception instanceof ExceptionInterface && !$exception instanceof NotFoundHttpException) {
            if (!config('app.debug')) {
                Log::channel('daily')->error('['.$exception->getCode().'] "'.$exception->getMessage().'" on line '.$exception->getLine().' of file '.$exception->getFile());
            } else {
                parent::report($exception);
            }
        }
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
        // return parent::render($request, $exception);
        $output = new StdClass;
        $output->responseCode = $exception instanceof ExceptionInterface ? str_pad(strval($exception->getErrorCode()), 2, '0', STR_PAD_LEFT) : '99';
        $output->responseDesc = $exception->getMessage().($exception instanceof ExceptionInterface ? '' : (ENV('APP_DEBUG') === true ? ' '.$exception->getFile() : '')).' Ln.'.$exception->getLine();

        if($exception instanceof ExceptionInterface && $exception->getData() !== null) {
            $output->responseData = $exception->getData();
        }
        return response()->json($output, 200);
    }
}
