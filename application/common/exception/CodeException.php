<?php
namespace app\common\exception;


class CodeException extends LangException
{
    public function __construct($code, array $vars = [], \Throwable $previous = null)
    {
        $message = 'E'. $code;
        parent::__construct($message, $code, $vars, $previous);
    }
}