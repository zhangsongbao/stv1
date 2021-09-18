<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 应用公共文件
use think\exception\HttpResponseException;
use think\Response;
use think\facade\Cache;
if (!function_exists('__')) {
    /**
     * @param $name
     * @param array $vars
     * @param string $lang
     * @return int|mixed|string
     * @author : yhc

     * @date: 2020/10/16 上午9:00
     * @name:返回对应语言
     */
    function __($name, $vars = [], $lang = '')
    {
        if (is_numeric($name) || !$name) {
            return $name;
        }
        if (!is_array($vars)) {
            $vars = func_get_args();
            array_shift($vars);
            $lang = '';
        }
        return lang($name, $vars, $lang);
    }
}
if (!function_exists('auth')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed+
     */
    function auth($key = '')
    {
        return \hnzl\HnzlAuth::getInstance($key);
    }
}

if (!function_exists('logs')) {
    /**
     * 获取语言变量值
     * @param string $name 语言变量名
     * @param array $vars 动态变量值
     * @param string $lang 语言
     * @return mixed+
     */
    function logs($key = '')
    {
        return \hnzl\HnzlLog::getInstance();
    }
}

if (!function_exists('success')) {
    /**
     * 操作成功返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 成功码，默认为0
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    function success($msg = '', $data = [], $code = 0, $type = 'json', array $header = [])
    {
        result($msg, $data, $code, $type, $header);
    }
}

if (!function_exists('error')) {
    /**
     * 操作失败返回的数据
     * @param string $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为1
     * @param string $type 输出类型
     * @param array $header 发送的 Header 信息
     */
    function error($msg = '', $data = [], $code = 1, $type = 'json', array $header = [])
    {
        result($msg, $data, $code, $type, $header);
    }
}

if (!function_exists('result')) {
    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param mixed $msg 提示信息
     * @param mixed $data 要返回的数据
     * @param int $code 错误码，默认为0
     * @param string $type 输出类型，支持json/xml/jsonp
     * @param array $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    function result($msg, $data = null, $code = 0, $type = 'json', array $header = [])
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];
        // 如果未设置类型则自动判断
        //$type = 'json';
        if (isset($header['statuscode'])) {
            $code = $header['statuscode'];
            unset($header['statuscode']);
        } else {
            //未设置状态码,根据code值判断
            $code = $code >= 1000 || $code < 200 ? 200 : $code;
        }
        $response = Response::create($result, $type, $code)->header($header);
        throw new HttpResponseException($response);
    }
}

if (!function_exists('mkdirs')) {
    /**
     * @param $path
     * @return bool
     * @author : yhc

     * @date: 2020/10/14 上午11:25
     * @name: 创建文件夹
     */
    function mkdirs($path)
    {
        if (is_dir($path)) {
            return true;
        } else {
            if (mkdir($path, 0777, true)) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if (!function_exists('redis')) {
    /**
     * @param string     $name 缓存名称
     * @param mixed     $value 缓存值
     * @param mixed     $options 缓存参数
     * @param string    $tag 缓存标签
     * @return bool|mixed
     * @author : yhc

     * @date: 2020/11/04 10:45
     * @name: 缓存管理
     */
    function redis(string $name, $value = '',$options = null,string $tag = null)
    {

        if ('' === $value) {
            // 获取缓存
            return 0 === strpos($name, '?') ? Cache::store('redis')->has(substr($name, 1)) : Cache::store('redis')->get($name);
        } elseif (is_null($value)) {
            // 删除缓存
            return Cache::store('redis')->rm($name);
        }
        // 缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : null; //修复查询缓存无法设置过期时间
        } else {
            $expire = is_numeric($options) ? $options : null; //默认快捷缓存设置过期时间
        }

        if (is_null($tag)) {
            return Cache::store('redis')->set($name, $value, $expire);
        } else {
            return Cache::store('redis')->tag($tag)->set($name, $value, $expire);
        }
    }
}


if (!function_exists('hashids')) {
    /**
     * @param string $str
     * @param string $type
     * @param string $userType
     * @return array|string
     * @author : yhc

     * @date: 2020/11/11 11:25
     * @name:  字符串加解密函数
     */
    function hashids(string  $str,string $type='encode',string $userType='user_id')
    {
        static $hashids =null;
        if(!$hashids){
            $hashids=new  \Hashids\Hashids(config('hashid.'.$userType.'.salt'),config('hashid.'.$userType.'.length'));
        }
        //加密
        if($type=='encode'){
            return $hashids->encode($str);
        }else{
            //解密
            return $hashids->decode($str);
        }

    }
}

if (!function_exists('authCode')) {
    /**
     * @param string $str
     * @param string $type
     * @param int $expiry
     * @author : yhc

     * @date: 2020/11/19 09:26
     * @name: 加解密字符串
     */
    function authCode(string  $string,string $type='encode',$key='', $expiry = 0)
    {
            // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
            $ckey_length = 4;
            $key=$key?$key:config('hashid.downloadParams.salt');
            // 密匙
            // 密匙a会参与加解密
            $keya = md5(substr($key, 0, 16));
            // 密匙b会用来做数据完整性验证
            $keyb = md5(substr($key, 16, 16));
            // 密匙c用于变化生成的密文
            $keyc = $ckey_length ? ($type == 'decode' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
            // 参与运算的密匙
            $cryptkey = $keya.md5($keya.$keyc);
            $key_length = strlen($cryptkey);
            // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
            //解密时会通过这个密匙验证数据完整性
            // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
            $string = $type == 'decode' ? base64_decode(substr($string, $ckey_length)) :  sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
            $string_length = strlen($string);
            $result = '';
            $box = range(0, 255);
            $rndkey = array();
            // 产生密匙簿
            for($i = 0; $i <= 255; $i++) {
                $rndkey[$i] = ord($cryptkey[$i % $key_length]);
            }
            // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
            for($j = $i = 0; $i < 256; $i++) {
                $j = ($j + $box[$i] + $rndkey[$i]) % 256;
                $tmp = $box[$i];
                $box[$i] = $box[$j];
                $box[$j] = $tmp;
            }
            // 核心加解密部分
            for($a = $j = $i = 0; $i < $string_length; $i++) {
                $a = ($a + 1) % 256;
                $j = ($j + $box[$a]) % 256;
                $tmp = $box[$a];
                $box[$a] = $box[$j];
                $box[$j] = $tmp;
                // 从密匙簿得出密匙进行异或，再转成字符
                $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
            }
            if($type == 'decode') {
                // 验证数据有效性，请看未加密明文的格式
                if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&  substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                    return substr($result, 26);
                } else {
                    return ;
                }
            } else {
                // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
                // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
                return $keyc.str_replace('=', '', base64_encode($result));
            }
 

    }
}

if (!function_exists('task')) {
    /**
     * @param $class
     * @param $function
     * @param $data
     * @author : yhc

     * @date: 2020/11/12 17:23
     * @name: 用于执行一些非即时任务、 方便后期可改为swool异步处理
     */
    function task($class,$function,$data)
    {
        static $taskClass =null;
        if(!$taskClass){
            $taskClass=new $class();
        }
        if(method_exists($taskClass,$function)){
            $taskClass->$function($data);
        }
    }
}


if (!function_exists('codeToText')) {
    /**
     * @param $arr
     * @param $config
     * @author : yhc

     * @date: 2020/11/19 11:35
     * @name: code值数组 转汉字
     */
    function codeToText($arr,$config,$key)
    {
        $str='';
        foreach ($arr as $k=>$v){
            $str.=$config[$v[$key]]?$config[$v[$key]].',':'';
        }
        return trim($str,',');
    }
}


if (!function_exists('removeKeys')) {
    /**
     * @param $arr
     * @param $config
     * @author : yhc

     * @date: 2020/11/19 11:35
     * @name: 移除数组指定元素
     */
    function removeKeys($arr,$keys)
    {
        foreach ($keys as $v){
            if(isset($arr[$v])){
                unset($arr[$v]);
            }
        }
        return $arr;
    }
}

