<?php

namespace Payment\Exceptions;

use Exception;

class AuthorizeException extends Exception
{
    protected $code = 403;
    protected $message = 'Forbidden';
}