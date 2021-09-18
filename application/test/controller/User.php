<?php
/**
 * @author : yhc

 * @date : 2020/10/10 8:52
 * @name :
 */

namespace app\test\controller;
use hnzl\HnzlController;
use hnzl\model\AuthGroup;
class User extends HnzlController
{
    protected $noNeedLogin = ['login','vue','text','reg'];
    protected $noNeedRight = ['getUser','userMsg','selectUser','selectAllUser'];
    public $childrenGroupIds=[];
    public $ChildrenUserIds=[];
    public $field='id,user_name,real_name,mobile,status,weigh,avater';
    public $auth='userText';
    protected $import = [
        'limit' => '1000',
        'type' => 'saveAll',
    ];
    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model=model('hnzl\model\User');
        $this->validate=new \app\admin\validate\User();
    }


    public function login(){
        try{
            $user_name=$this->request->post('user_name');
            $password=$this->request->post('password');
            if(empty($user_name)){
                throw new \Exception(__('Require',['s'=>__('user_name')]));
            }
            $where=[
                'user_name|mobile'=>$user_name,
                'is_delete'=>0
            ];
            $user=$this->model->where($where)->field('id,user_name,real_name,password,salt,mobile,status')->find();
            if(!$user){
                throw new \Exception(__('User Not Exists'));
            }
            $password=$this->model->getPassword($password,$user['salt']);
            if($password!=$user['password']){
                logs()->log(['login'=>$user,'msg'=>__('Login Error')],10,$user);
                throw new \Exception(__('Login Error'));
            }
            if($user['status']=='1'){
                logs()->log(['login'=>$user,'msg'=>__('Login Forbid')],10,$user);
                throw new \Exception(__('Login Forbid'));
            }
        }catch(\Exception $e){
            error($e->getMessage(),'',2);
        }
        $token=auth($this->auth)->getToken($user);
        logs()->log(['login'=>$user],0,$user);
        success('success',['token'=>$token,'refresh_token'=>'']);
    }
    public function reg(){
        $insert=$this->request->post();
        if ($this->validate) {
            if (!$this->validate->scene('add')->check($insert)) {
                error($this->validate->getError(), '', 2);
            }
        }
        if(isset($insert['password']) &&!empty($insert['password'])){
            $insert=array_merge($insert,$this->model->getPassword($insert['password']));
        }else{
            $insert['password']='';
        }
        $res=$this->model->allowField(true)->save($insert);
        if($res){
            success(__('Success'),$res);
        }
        error(__('Error'),$res);
    }

}