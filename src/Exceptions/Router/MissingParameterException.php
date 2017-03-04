<?php

namespace App\Exceptions\Router;

class MissingParameterException extends \Exception
{
    public function __construct(string $controller, string $action, \ReflectionParameter $parameter)
    {
        $message = "Parameter '%s' missing from controller method %s::%s";
        $message = sprintf($message, $parameter->getName(), get_class($controller), $action);

        parent::__construct($message);
    }
}
