<?php
/**
 * @author : yhc

 * @date : 2020/10/14 9:20
 * @name :
 */
namespace app\log\logic;
use hnzl\HnzlModel;
use app\log\model\Log as LogModel;
class Log{
    public static function insert($data=[],$type,$user){
        $post=request()->post();
        //不记录导入
        unset($post['data']);
        $insert=[
            'content'=>json_encode($data,true),
            'module'=>request()->module(),
            'ip'=>request()->ip(),
            'controller'=>lcfirst(request()->controller()),
            'action'=>request()->action(true),
            'params'=>json_encode($post,true),
            'user_id'=>$user['id'],
            'user_name'=>$user['user_name'],
            'type'=>$type,
            'create_time'=>time()
        ];
        $LogModel=new LogModel();
        return $LogModel->save($insert);
    }
}