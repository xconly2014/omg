<?php
namespace app\common\service;


use app\lib\DocParser;
use think\Config;
use think\Loader;

class Dom
{
    protected $dom_dir;
    protected $docParser;

    public function init($dom_dir = null) {
        $dom_dir || $dom_dir = API_ROOT_PATH  .DS .'dom';
        $this->dom_dir = $dom_dir;
        return $this;
    }

    public function getDocParser() {
        if(!$this->docParser) {
            $this->docParser = new DocParser();
        }
        return $this->docParser;
    }

    public function parser($ref) {
        return $this->getDocParser()->parse($ref->getDocComment());
    }

    /**
     * 获取API 控制器列表
     * @return array
     */
    public static function getApiControllers() {
        $controllers = \think\Config::get('controller');
        $list = [];
        $docParser = new \app\lib\DocParser();
        if(!empty($controllers)) {
            foreach ($controllers as $controller => $title) {
                if(class_exists($controller)) {
                    $reflection = new \ReflectionClass($controller);
                    $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $actions = [];
                    foreach ($methods as $method){
                        $doc_str = $method->getDocComment();
                        if($doc_str) {
                            $action_doc = $docParser->parse($doc_str);
                            if(isset($action_doc['title'])) {
                                $action_doc[ 'controller' ] = $controller;
                                $action_doc[ 'method' ] = $method->name;
                                $actions[] = $action_doc;
                            }
                        }
                    }
                    if(!empty($actions)) {
                        isset($list[ $title ]['action']) && $actions = array_merge($list[ $title ]['action'], $actions);
                        $list[ $title ] = [
                            'title' => $title,
                            'action' => $actions,
                        ];
                    }
                }
            }
        }
        return $list;
    }

    /**
     * 通过控制器名读取dom 并加以整理
     * @param $controller
     * @param $action
     * @return array|bool
     */
    public function readByController($controller, $action) {
        $doc = $this->parserAction($controller, $action);
        if(!$doc) {
            return false;
        }
        if(!$doc['doc']) {
            return false;
        }

        $dom = $this->read($doc['doc']);
        if(!$dom) {
            return false;
        }
        $dom = array_merge($doc, $dom);

        isset($dom['param']) || $dom['param'] = [];
        $common_param = Config::get('common_param');
        if(!empty($common_param)) {
            $dom['param'] = array_merge($common_param, $dom['param']);
        }

        # 通过 validate 设置参数的 require， type 等属性
        if(!empty($dom['param'])) {
            $validate = false;
            $validate_class_name = isset($dom['validate']) && $dom['validate'] ? $dom['validate'] : false;
            if($validate_class_name) {
                if (class_exists($validate_class_name)) {
                    $validate = new $validate_class_name;
                    $dom['scope'] = isset($validate->scope) ? $validate->scope : [];
                }
            }
            foreach ($dom['param'] as $key => $info) {
                $dom['param'][$key]['name'] = $key;
                if($validate) {
                    if(  isset($validate->rule[ $key ]) ) {
                        $rule = $validate->rule[ $key ];
                        $dom['param'][$key]['require'] = stripos($rule, 'require\'') === false && stripos($rule, 'require|') === false ? 0 : 1;
                        $dom['param'][$key]['type'] = stripos($rule, 'number') === false && stripos($rule, 'integer') === false ? 'String' : 'Number';
                    } else {
                        unset( $dom['param'][$key] );
                    }
                } else {
                    $dom['param'][$key]['require'] = 0;
                    $dom['param'][$key]['type'] = 'String';
                }
            }
        }

        return $dom;
    }

    /**
     * 解析控制器方法注释
     * @param $controller
     * @param $action
     * @return array|bool
     */
    public function parserAction($controller, $action) {
        if(is_string($controller) && !class_exists($controller)) {
            return false;
        }
        $refClass = new \ReflectionClass( $controller );
        if(!$refClass->hasMethod($action)) {
            return false;
        }

        $refMethod = $refClass->getMethod($action);
        $doc = $this->parser($refMethod);
        return $doc + [
                'title' => null,
                'doc' => null,
                'validate' => null
            ];
    }

    /**
     * 生成控制器方法注释中的验证器
     * @param $controller
     * @param $action
     * @return false|null|\think\Validate
     */
    public function newActionValidator($controller, $action) {
        $doc = $this->parserAction($controller, $action);
        if( isset($doc['validate']) && $doc['validate']) {
            class_exists("\\" . $doc['validate']) && $doc['validate'] = "\\" . $doc['validate'];
            if(class_exists( $doc['validate'])) {
                return Loader::validate($doc['validate']);
            }
        }
        return null;
    }

    /**
     * 读取 dom 文件
     * @param $filename
     * @return bool|array
     */
    public function read($filename) {
        $filename = $this->realFilename($filename);
        if(file_exists($filename)) {
            return include $filename;
        }
        return false;
    }

    /**
     * 写 dom 文件
     * @param $filename
     * @param array $data
     * @return bool|int
     */
    public function write($filename, array $data) {
        $filename = $this->realFilename($filename);
        $this->chkMkdir($filename);
        return file_put_contents($filename,$this->array4put($data));
    }

    protected function realFilename($filename) {
        if(strpos($filename, '.php') === false) {
            $filename .= '.php';
        }
        $filename = $this->dom_dir . DS .$filename;
        return $filename;
    }

    /**
     * 数组转字符串
     * @param array $data
     * @param int $indents
     * @return string
     */
    protected function array4put(array $data, $indents = 1) {
        $i_str_0 = str_repeat(' ', $indents * 4 - 4);
        $i_str = $i_str_0 . '    ';
        $content = 1==$indents ? "<?php\r\n\r\nreturn [\r\n" : "[\r\n";
        foreach ($data as $k => $v) {
            if(is_array($v)) {
                $content .= $i_str . "'{$k}' => " . $this->array4put($v, $indents + 1);
            } elseif (is_string($v)) {
                $content .= $i_str . "'{$k}' => '{$v}',\r\n";
            } else {
                if(is_bool($v)) {
                    $v = $v ? 'true' : 'false';
                }
                $content .= $i_str . "'{$k}' => {$v},\r\n";
            }
        }
        $content .= 1==$indents ? '];' : $i_str_0 . "],\r\n";
        return $content;
    }

    /**
     * 检查文件目录是否存在，不存在创建之
     * @param $filename
     */
    protected function chkMkdir($filename) {
        $dir = dirname($filename);
        if(!file_exists($dir)) {
            $p_dir = dirname($dir);
            if(!file_exists($p_dir)) {
                $this->chkMkdir($p_dir);
            }
            mkdir($dir);
        }
    }
}