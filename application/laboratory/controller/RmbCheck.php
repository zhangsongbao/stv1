<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-24 09:24:43
 */

namespace app\laboratory\controller;
use app\laboratory\model\RmbDetail;
use app\laboratory\model\SiloMar;
use hnzl\HnzlController;

//生产配比控制器
class RmbCheck extends HnzlController
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];


    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model = model('app\laboratory\model\RecipeMaterialBase');
        //载入验证器
        $this->validate = new \app\laboratory\validate\RecipeMaterialBase();
        $this->LoadLang('RecipeMaterialBase');
    }

    /**
     * @param $where
     * @param $model
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where, &$model, $with = true)
    {
        //查询当前料位 下配方信息
//        $hostCode = $this->request->post('filter.host_code');
//        $stationCode = $this->request->post('filter.station_code');
//        if (!$hostCode || !$stationCode) {
//            error(__('Require', ['s' => __('station_code').__('Or').__('host_code')]));
//        }
        $real_name=$this->request->post('user.real_name');
        if($real_name){
            $user= new \app\admin\logic\User();
            $users=$user->users([['real_name','like','%'.$real_name.'%']],'id');
            if($users){
                $where[]=['update_user','in',$users];
            }else{
                $where[]=['rmb_id','<','0'];
            }
        }
        $model = $model->with(['rmbDetail','user']);
    }

    public function status(){
        $update['status']=$this->request->post('status/d');
        $update['rmb_id']=$this->request->post('rmb_id/d');
        if(!$update['status']){
            error(__('Require', ['s' => __('status')]));
        }
        if(!$update['rmb_id']){
            error(__('Require', ['s' => __('rmb_id')]));
        }
        $update['check_user']=$this->userId;
        if($this->model->isUpdate(true)->save($update)){
            success(__('Success'));
        }
        error(__('Error'));
    }
}
