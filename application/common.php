<?php
// 应用公共文件

define('API_ROOT_PATH', __DIR__ . DS . 'api');
define('MCH_ROOT_PATH', __DIR__ . DS . 'mch');

// 应用公共文件
function M($name, $common = 'common', $appendSuffix = false ) {
    return \think\Loader::model($name, 'model', $appendSuffix, $common);
}

function R($name, $common = 'common', $appendSuffix = false) {
    return \think\Loader::model($name, 'redis', $appendSuffix, $common);
}

function S($name, $common = 'common', $appendSuffix = false) {
    return \think\Loader::model($name, 'service', $appendSuffix, $common);
}


function completeUrl($value, $suffix = false) {
    if($value) {
        $info = parse_url($value);
        if(!isset($info['host'])) {
            $value = url($value, '', $suffix, true);
        }
    }
    return $value;
}

/**
 * @param int $id
 * @param int $ml
 * @return string 至少比 $ml 多2位
 */
function id2id($id, $ml = 716928) {
    $m = bcadd(bcmul($ml, 10), 7); //$m 结尾 1，3，7，9
    $l = strlen($m);
    $mod = bcpow(10, $l);
    $id = bcadd($id, $mod); // $id 至少比 $m 多1位

    $a = substr($id, 0, 0-$l);
    $b = substr($id, 0-$l);

    $r1 = bcmul($m, $b, 0);
    $r1 = bcmod($r1, $mod); // $m， 10 ** $l 互质
    $r2 = bcmul(9, $r1, 0);
    $r2 = bcmod($r2, $mod);

    $r = str_pad($r2, $l, '0', STR_PAD_LEFT);
    return $a.$r;
}


function id2str($id) {
    $tuple = 'hY6wQ3Wcd8CD5FabAuBioEkT04VeGlnXZNj2IJOvKLqrspUtPSMfxyz7m9gH1R';

    $str = '';
    while (true) {
        if($id == 0) {
            break;
        }
        $mod = bcmod($id, 62);
        $id = bcdiv( bcsub($id, $mod), 62 );

        $str .= $tuple[ $mod ];
    }
    return $str;
}

function str2id($str) {
    $tuple = 'hY6wQ3Wcd8CD5FabAuBioEkT04VeGlnXZNj2IJOvKLqrspUtPSMfxyz7m9gH1R';

    $id = 0;
    foreach (str_split($str) as $i => $s) {
        $lm = strpos($tuple, $s);
        $rm = bcpow(62,  $i);
        $ra = bcmul($lm, $rm);
        $id = bcadd($id, $ra);
    }
    return $id;
}

/**
 * 生成一个随机字母数字的字符串（长度16）
 * @param $salt
 * @param $length
 * @return mixed
 */
function randomHash($salt, $length = 16) {
    switch ($length) {
        case 32:
            $id = md5(uniqid(mt_rand(), true).'$', true); // 16
            $sum = substr( md5($id.$salt, true), 4, 8); // 8
            break;
        case 16:
        default:
            $id = substr( md5(uniqid(mt_rand(), true).'$', true), 4, 8); // 8
            $sum = substr( md5($id.$salt, true), 4, 4); // 4
    }
    $str = base64_encode($id.$sum);
    return str_replace(['/', '+'], ['-', '_'], $str);
}

/**
 * 验证hash值的校验和
 * @param $hash
 * @param $salt
 * @param $length
 * @return bool
 */
function chkRandomHashSum($hash, $salt, $length = 16) {
    $base64 = str_replace(['-', '_'], ['/', '+'], $hash);
    $str = base64_decode($base64);

    switch ($length) {
        case 32:
            $id = substr($str, 0, 16);
            $sum = substr($str, 16);
            $sample = substr( md5($id.$salt, true), 4, 8);
            break;
        case 16:
        default:
            $id = substr($str, 0, 8);
            $sum = substr($str, 8);
            $sample = substr( md5($id.$salt, true), 4, 4);
    }
    return $sum == $sample;
}

function channelToCountry($channel) {
    $country = null;
    $cs = explode('-', $channel);
    if (in_array('jp', $cs)) {
        $country = 'ja-JP';
    }
    return $country;
}

/**
 * 过滤4字节UTF-8 以避免mysql保存报错
 * @param $str
 * @return string
 */
function filterUtf8($str)
{
    /*utf8 编码表：
    * Unicode符号范围           | UTF-8编码方式
    * u0000 0000 - u0000 007F   | 0xxxxxxx
    * u0000 0080 - u0000 07FF   | 110xxxxx 10xxxxxx
    * u0000 0800 - u0000 FFFF   | 1110xxxx 10xxxxxx 10xxxxxx
    *
    */
    $re = '';
    $str = str_split(bin2hex($str), 2);

    $mo =  1<<7;
    $mo2 = $mo | (1 << 6);
    $mo3 = $mo2 | (1 << 5);         //三个字节
    $mo4 = $mo3 | (1 << 4);          //四个字节
    $mo5 = $mo4 | (1 << 3);          //五个字节
    $mo6 = $mo5 | (1 << 2);          //六个字节


    for ($i = 0; $i < count($str); $i++)
    {
        if ((hexdec($str[$i]) & ($mo)) == 0)
        {
            $re .=  chr(hexdec($str[$i]));
            continue;
        }

        //4字节 及其以上舍去
        if ((hexdec($str[$i]) & ($mo6) )  == $mo6)
        {
            $i = $i +5;
            continue;
        }

        if ((hexdec($str[$i]) & ($mo5) )  == $mo5)
        {
            $i = $i +4;
            continue;
        }

        if ((hexdec($str[$i]) & ($mo4) )  == $mo4)
        {
            $i = $i +3;
            continue;
        }

        if ((hexdec($str[$i]) & ($mo3) )  == $mo3 )
        {
            $i = $i +2;
            if (((hexdec($str[$i]) & ($mo) )  == $mo) &&  ((hexdec($str[$i - 1]) & ($mo) )  == $mo)  )
            {
                $r = chr(hexdec($str[$i - 2])).
                    chr(hexdec($str[$i - 1])).
                    chr(hexdec($str[$i]));
                $re .= $r;
            }
            continue;
        }



        if ((hexdec($str[$i]) & ($mo2) )  == $mo2 )
        {
            $i = $i +1;
            if ((hexdec($str[$i]) & ($mo) )  == $mo)
            {
                $re .= chr(hexdec($str[$i - 1])) . chr(hexdec($str[$i]));
            }
            continue;
        }
    }
    return $re;
}