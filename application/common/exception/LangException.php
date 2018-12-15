<?php
namespace app\common\exception;

use think\Lang;

class LangException extends ApiException
{
    /**
     * LangException constructor.
     * @param string $message
     * @param string $code
     * @param array $vars 变量替换
     * @param \Exception $previous
     */
    public function __construct($message, $code, array $vars = [], \Exception $previous = null)
    {
        $message = Lang::get($message, $vars);
        parent::__construct($message, $code, $previous);
    }
}