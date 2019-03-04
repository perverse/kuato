<?php

namespace Kuato\Exceptions;

use Exception;
use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PathCreationException extends HttpException 
{
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     */
    public function __construct($message = null)
    {
        parent::__construct(500, $message, null, [], 500);
    }
}
