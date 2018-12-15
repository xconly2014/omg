<?php
namespace app\api\controller;

class Wx extends ApiController
{
    /**
     * @title 小程序登录Code
     * @doc wx/onLogin
     * @validate app\api\service\wx\OnLogin
     */
    public function onLogin() {
        $data = S('wx.onLogin')->api();
        return $this->response($data);
    }

    /**
     * @title 小程序解密
     * @doc wx/decode
     * @validate app\api\service\wx\Decode
     */
    public function decode() {
        $data = S('wx.decode')->api();
        return $this->response($data);
    }

    /**
     * @title 发送验证码
     * @doc wx/authCode
     * @validate app\api\service\wx\AuthCode

    public function authCode() {
        $data = S('wx.authCode')->api();
        return $this->response($data)->cache();
    }*/

    /**
     * @title 保存（获取）用户信息
     * @doc wx/userInfo
     * @validate app\api\service\wx\UserInfo
     */
    public function userInfo() {
        $data = S('wx.userInfo')->api();
        return $this->response($data);
    }

    /**
     * @title 保存手机号
     * @doc wx/userTel
     * @validate app\api\service\wx\UserTel
     */
    public function userTel() {
        $data = S('wx.userTel')->api();
        return $this->response($data);
    }
    /**
     * @title 我的
     * @doc wx/my
     * @validate app\api\service\wx\My
     */
    public function my() {
        $data = S('wx.my')->api();
        return $this->response($data);
    }

    /**
     * @title 我的推荐列表
     * @doc wx/referrer
     * @validate app\api\service\wx\Referrer
     */
    public function referrer() {
        $data = S('wx.referrer')->api();
        return $this->response($data)->cache();
    }

    /**
     * @title 签到信息
     * @doc wx/attendInfo
     * @validate app\api\service\wx\AttendInfo
     */
    public function attendInfo() {
        $data = S('wx.attendInfo')->api();
        return $this->response($data);
    }

    /**
     * @title 签到
     * @doc wx/attend
     * @validate app\api\service\wx\Attend
     */
    public function attend() {
        $data = S('wx.attend')->api();
        return $this->response($data);
    }
}