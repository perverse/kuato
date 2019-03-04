<?php

namespace Kuato\Middleware;

use Closure;
use Kuato\Services\ServiceResponseFormatter;
use Kuato\Containers\ServiceResponse;
// @todo: make this a non-concrete type

class ApiResponseJson
{
    protected $formatter;

    public function __construct(ServiceResponseFormatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if (isset($response->original) && $response->original instanceof ServiceResponse) {
            $response = $this->formatter->make($response->original)->toJsonResponse();
        }

        $response->headers->set('P3P', 'CP="This site does not have a p3p policy."');

        return $response;
    }
}