<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/11/13 08:52
 * @name:
 */
namespace  app\admin\logic;
use app\admin\model\UserFile as ufModel;
class UserFile{
    protected  $model=null;
    public function __construct(){
        //载入model
        $this->model=new ufModel();
    }


    /**
     * @param $data
     * @author : yhc
     
     * @date: 2020/11/13 09:18
     * @name: 新增用户记录
     */
    public function add($user_id){
        $userConfig=\app\admin\model\Config::config('upload',$value='name,value');
        $userConfig['user_id']=$user_id;
        return $this->model->allowField(true)->isUpdate(false)->data($userConfig)->save();
    }
}