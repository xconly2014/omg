<?php


namespace app\open\controller;




class Login extends  AbstractController
{
    public function index() {
        return $this->fetch();
    }

    public function ajaxLogin() {
        $post = $this->request->post();
        $err = 'Invalid request!';
        if(isset($post['data']) && isset($post['__token__'])) {
            try {
                $content = openssl_decrypt($post['data'], 'AES-256-ECB', $post['__token__'], 0, '');
            } catch (\Exception $e) {
                $this->result($this->request->token(), 0, 'Invalid arguments');
            }

            if($data = json_decode($content, true)) {
                $data['__token__'] = $post['__token__'];
                $err = $this->validateLoginFormData($data);
                if($err === true) {
                    $expire = isset($data['remember_me']) ? 86400 * 7 : 0;
                    $err = S('access')->doLogin($data['account'], $data['password'], $expire);
                    if($err === true) {
                        $this->result('', 1);
                    }
                }
            }
        }
        $this->result($this->request->token(), 0, $err);
    }




    /**
     * 验证表单
     * @param $data
     * @return string|true
     */
    protected function validateLoginFormData($data) {
        return $this->validate($data, [
            'account' => 'require|email|token',
            'password' => 'require'
        ],[
            'account.email' => 'Wrong email address',
        ]);
    }
}