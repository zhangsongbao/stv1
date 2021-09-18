<?php
namespace hnzl;
use app\log\logic\Log;

/**
 * @author : yhc

 * @date : 2020/10/9 7:57
 * @name :
 */
class HnzlLog{
    protected static $instance;
    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * @param array $data 要记录的数据 必须
     * @param int $type  类型  新增1，删除2,批量删除22，修改3，批量修改33，查询列表4，查看(单条)44，  5导入  6导出
     * @param string $auth token
     * @throws \think\exception\ClassNotFoundException
     */
    public function log($data=[],$type=4,$user=[]){
        if(!$user){
            $user['id']=auth()->user('id');
            $user['user_name']=auth()->user('user_name');
        }
        if(is_string($user)){
            $user['id']=auth($user)->user('id');
            $user['user_name']=auth($user)->user('user_name');
        }
        return Log::insert($data,$type,$user);
    }
    public function fileLog($data=[],$type=''){
        $dir=request()->module().'/'.request()->controller().'/'.request()->action();
        mkdirs('../runtime/logs/'.$dir);
        $content = date('H:i:s') . ':' . json_encode($data) . PHP_EOL;
        $name= date('Ymd') .'-'.$type.'-'.auth()->user('id');
        $file='../runtime/logs/'.$dir.'/'.$name. '.txt';
        file_put_contents($file, $content, FILE_APPEND);
        return $file;
    }


}