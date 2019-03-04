<?php

namespace Kuato\Exceptions;

use Exception;
use ReflectionClass;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if (method_exists($exception, 'getStatusCode')):
            $status_code = $exception->getStatusCode();
        else:
            $status_code = 500;
        endif;

        if (true) {

            $meta = [
                'error_type' => get_class($exception),
                'code' => $status_code
            ];

            if (method_exists($exception, 'hasErrors')):
                if ($exception->hasErrors()):
                    $meta['meta']['errors'] = $exception->getErrors();
                endif;
            endif;

            if (config('app.env') == 'local'):
                $meta['debug'] = [
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'trace' => $exception->getTrace(),
                ];
            endif;

            $response = [
                'meta' => $meta,
                'errors' => [method_exists($exception, 'hasErrors') && $exception->hasErrors() ? $exception->getErrors() : $exception->getMessage()]
            ];

            // @todo refactor this to return a ServiceResponse object for common API formatting
            return response()->json($response, $status_code);
        } else {
            $titles = [
                "We've done a paw job",
                "Simply a-paw-ling",
                "A total cat-astrophe",
                "You've gotta be kitten me",
            ];

            $available_codes = [
                100, 101, 200, 201, 202, 204, 206, 207, 300, 301, 302,
                303, 304, 305, 307, 400, 401, 402, 403, 404, 405, 406,
                408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418,
                420, 421, 422, 423, 424, 425, 426, 429, 431, 444, 450,
                451, 500, 502, 503, 504, 506, 507, 508, 509, 511, 599
            ];

            if (in_array($status_code, $available_codes)) {
                return response(view('kuato::exceptions.exception', [
                    'code' => $status_code,
                    'title' => $titles[rand(0, count($titles) - 1)]
                ]), $status_code);
            }
        }
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $status_code = 401;

        $meta = [
            'meta' => [
                'error_type' => $reflect->getShortName(),
                'code' => $status_code,
                'error_message' => ($exception->getMessage() ? $exception->getMessage() : $reflect->getShortName()),
            ]
        ];

        return response()->json($meta, $status_code);
    }
}
