<?php
namespace app\common\exception;


class Factory
{
    /**
     * 系统异常
     * @param \Exception $e
     * @return ApiException
     */
    public static function sysError(\Exception $e) {
        return new LangException('System error', 10000, [], $e);
    }

    /**
     * 登录过期， refresh_token 失效
     */
    public static function tokenExpired() {
        return new LangException('Please logon again', 10001);
    }

    /**
     * 其他错误
     * @param $message
     * @param int $code
     * @return LangException
     */
    public static function error($message, $code = 10002) {
        return new LangException($message, $code);
    }

    /**
     * 非法请求 - 内容
     * @return ApiException
     */
    public static function invalidRequestContent() {
        return new LangException('invalid request', 11000);
    }

    /**
     * 非法请求 - 签名
     * @return ApiException
     */
    public static function invalidRequestSign() {
        return new LangException('invalid request', 11001);
    }

    /**
     * 无效的请求类型
     * @return ApiException
     */
    public static function invalidRequestMethod() {
        return new LangException('invalid Request method', 11002);
    }

    /**
     * invalid token
     * @param string $message
     * @return LangException
     */
    public static function invalidToken($message) {
        return new LangException($message, 11003);
    }


    /**
     * 使用 Validator 验证参数未通过
     * @param $message
     * @return ApiException
     */
    public static function validateError($message) {
        return new LangException($message, 12000);
    }

    /**
     * 缺少必要参数 $attribute
     * @param $attribute
     * @return CodeException
     */
    public static function missParam($attribute) {
        return new CodeException(12001, ['attribute'=>$attribute]);
    }

    /**
     * 参数 attribute 校验失败
     * @param $attribute
     * @return CodeException
     */
    public static function failParam($attribute) {
        return new CodeException(12002, ['attribute' => $attribute]);
    }

    /**
     * 参数 attribute 解码失败
     * @param $attribute
     * @return CodeException
     */
    public static function failDecodeParam($attribute) {
        return new CodeException(12003, ['attribute' => $attribute]);
    }


    /**
     * 资源 name 不存在
     * @param $name
     * @return CodeException
     */
    public static function resourceNotFound($name) {
        return new CodeException(13001, ['name'=>$name]);
    }

    /**
     * 资源 name 已存在
     * @param $name
     * @return CodeException
     */
    public static function resourceExisted($name) {
        return new CodeException(13002, ['name'=>$name]);
    }

    /**
     * 资源 name 已被使用
     * @param $name
     * @return CodeException
     */
    public static function resourceBeUsed($name) {
        return new CodeException(13003, ['name'=>$name]);
    }
}