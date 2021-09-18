<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/10/28 10:56
 * @name:
 */
namespace  hnzl\model;
use app\admin\model\UserStation;
use app\admin\model\UserType;
use hnzl\HnzlModel;

class User extends HnzlModel{
    public $allowField=true;
    public $softDelete='is_delete';
    public function userType()
    {
        return $this->hasMany('app\admin\model\UserType','user_id','id');
    }
    public function userStation(){
        return $this->hasMany('app\admin\model\UserStation','user_id','id');
    }
    public function userGroup(){
        return $this->hasMany('AuthGroupAccess','user_id','id');
    }
    public function userAvater(){
        return $this->hasOne('app\admin\model\Attachment','id','avater')->bind(['avater_url'=>'url']);
    }
    public function userFile(){
        return $this->hasOne('app\admin\model\UserFile','user_id','id');
    }
    /**
     * @param array $where
     * @param string $field
     * @return array
     * @author : yhc
     
     * @date: 2020/11/10 09:44
     * @name: 获取用户
     */
    public function users($where=array(),$field='id,real_name'){
        $where[]=['is_delete','=',0];
        return  self::where($where)->column($field);
    }
    /**
     * @param $password
     * @param string $salt
     * @return string
     * @author : yhc
     
     * @date: 2020/11/04 10:07
     * @name: 创建密码
     */
    public function getPassword($password,$salt=''){
        //空密码
//        if(empty($password)){
//            return $password;
//        }
        if(empty($salt)){
            $strs="QWERTYUIOPASDFGHJKLZXCVBNM1234567890qwertyuiopasdfghjklzxcvbnm";
            $user['salt']=substr(str_shuffle($strs),mt_rand(0,strlen($strs)-11),10);
            $user['password']=md5('hnzl_user_'.$user['salt'].$password);
            return $user;
        }else{
            $user['password']=md5('hnzl_user_'.$salt.$password);
            return $user['password'];
        }
    }

    /**
     * @param $array
     * @param $userId
     * @param bool/array $delete
     * @throws \Exception
     * @author : yhc
     
     * @date: 2020/11/04 10:07
     * @name: 修改/新增用户关联操作
     */
    public  function  userChange($array,$delete=false){
        if(!isset($array['id'])){
            return false;
        }
        $userId=$array['id'];
        if($delete){
            UserStation::where(['user_id'=>$userId])->delete();
            UserType::where(['user_id'=>$userId])->delete();
            AuthGroupAccess::where(['user_id'=>$userId])->delete();
        }
        if(isset($array['user_station']) && is_array($array['user_station'])){
            if(count($array['user_station'])>0) {
                foreach ($array['user_station'] as $k => $v) {
                    $insertStation[] = ['user_id' => $userId, 'user_station_id' => $v];
                }
                UserStation::insertAll($insertStation);
            }
        }
        if(isset($array['user_type']) && is_array($array['user_type'])){
            if(count($array['user_type'])>0) {
                foreach ($array['user_type'] as $k => $v) {
                    $insertType[] = ['user_id' => $userId, 'user_type_id' => $v];
                }
                UserType::insertAll($insertType);
            }

        }
        if(isset($array['user_group']) && is_array($array['user_group'])){
            if(count($array['user_group'])>0){
                foreach ($array['user_group'] as $k=>$v){
                    $insertGroup[]=['user_id'=>$userId,'group_id'=>$v];
                }
                AuthGroupAccess::insertAll($insertGroup);
            }
        }
    }

}