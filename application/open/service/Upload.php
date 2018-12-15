<?php


namespace app\open\service;


use think\Request;

class Upload
{
    protected $tmp_dir = DS .'static' . DS . 'images' . DS .'tmp';

    public function upload($name = 'file') {
        $file = Request::instance()->file($name);

        if($file){
            $info = $file->validate(['size'=>1048576,'ext'=>'jpg,png,jpeg'])->move(ROOT_PATH . 'public' . $this->tmp_dir);
            if($info){
                $file = str_replace("\\", "/", $this->tmp_dir . DS . $info->getSaveName());
                return $this->result([
                    'file' => $file
                ], 1);
            }else{
                return $this->result('', 0, $file->getError());
            }
        } else {
            return $this->result('', 0, 'File Non Found');
        }
    }

    /**
     * 清理临时目录
     */
    public function clean() {
        $this->emptyDir(ROOT_PATH . 'public' . $this->tmp_dir);
    }

    protected function result($data, $code = 0, $msg = '') {
        return compact('data', 'code', 'msg');
    }

    /**
     * 移动资源到目录
     * @param $url
     * @param $name
     * @param string $sub_dir
     * @return string
     */
    public function moveToDir($url, $sub_dir, $name = '') {
        if(stripos($url, 'static/images/upload')) {
            return $url;
        }

        if(stripos($url, 'static/images/tmp')) {
            $path = parse_url($url, PHP_URL_PATH);
            $path_info = pathinfo($path);
            if ($name) {
                $ext = isset($path_info['extension']) ? '.' .$path_info['extension'] : '';
                $name = md5($name) . $ext;
            } else {
                $name = $path_info['basename'];
            }

            $public_dir = ROOT_PATH . 'public';
            $filename =  $public_dir .$path;

            $sub = DS . 'static' . DS .'images' . DS .'upload';

            if(file_exists( $filename )) {
                $target_dir = $public_dir . $sub;
                if(! file_exists( $target_dir )) {
                    mkdir($target_dir );
                }
                $target_dir = $target_dir . DS . $sub_dir;
                if(! file_exists( $target_dir )) {
                    mkdir($target_dir );
                }

                if(file_exists( $target_dir )) {
                    $new_filename = $target_dir . DS . $name;
                    if(file_exists($new_filename)) {
                        unlink($new_filename);
                    }
                    if( rename($filename, $new_filename) ) {
                        $relative = str_replace($public_dir, "", $target_dir . DS . $name);
                        $relative = str_replace("\\", "/", $relative);
                        return $relative . '?t=' . time();
                    }
                }
            }
        }

        return null;
    }

    protected function emptyDir($directory, $del_self = false){
        if(file_exists($directory) && stripos($directory, ROOT_PATH) === 0){
            if($dir_handle=@opendir($directory)){
                while($filename=readdir($dir_handle)){
                    if($filename!='.' && $filename!='..'){
                        $subFile=$directory."/".$filename;
                        if(is_dir($subFile)){
                            $this->emptyDir($subFile, true);
                        }
                        if(is_file($subFile)){
                            unlink($subFile);
                        }
                    }
                }
                closedir($dir_handle);
                if($del_self) rmdir($directory);
            }
        }
    }

    public function getMime($urlOrFilename) {
        $image_info = getimagesize($urlOrFilename);
        return $image_info['mime'];
    }


    public function getDataURL($urlOrFilename) {
        $url = completeUrl($urlOrFilename);

        $result = $this->httpGet($url);
        if($result['content']) {
            $mime = isset($result['header']['Content-Type']) ? $result['header']['Content-Type'] : 'image/png';
            return "data:{$mime};base64," . base64_encode($result['content']);
        }
        return null;
    }


    /**
     * 发起请求
     * @param $url
     * @return array
     */
    protected function httpGet($url) {
        //初始化
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, true); // 返回头部
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// https请求不验证证书和hosts
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        //执行并获取HTML文档内容
        $output = curl_exec($ch);


        $header = [];
        $content = '';
        if(curl_errno($ch))
        {
            echo 'Curl error: ' . curl_error($ch);
        } else {
            // 获得响应结果里的：头大小
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            // 根据头大小去获取头信息内容
            $h = substr($output, 0, $headerSize);
            $content = substr($output, $headerSize);

            $hl = explode("\r\n", $h);

            foreach ($hl as $l) {
                if(strpos($l, ': ')) {
                    list($key, $value) = explode(': ', $l);
                    $header[$key] = $value;
                }
            }
        }
        //释放curl句柄
        curl_close($ch);

        return [
            'header' => $header,
            'content' => $content
        ];
    }
}