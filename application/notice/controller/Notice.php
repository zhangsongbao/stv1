<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-08 09:09:27
 */
namespace app\notice\controller;
use app\admin\logic\User;
use app\notice\model\NoticeDetail;
use hnzl\HnzlController;
class Notice extends HnzlController{
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
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\notice\model\Notice');
        //载入验证器
        $this->validate=new \app\notice\validate\Notice();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){

    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $insert['add_user']=$this->userId;
        $insert['add_user_name']=auth()->user('real_name');
        $insert['send_time']=$insert['send_time']?strtotime($insert['send_time']):time();
    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        //增加详情表发送
        $users=json_decode($insert['to_user'],true);
        $arr=$insert;
        $arr['up_time']=$arr['send_time'];
        unset($arr['id']);
        unset($arr['to_user']);
        unset($arr['send_time']);
        $userLogic=new User();
        $userArr=$userLogic->users([['id','in',$users]]);
        foreach ($users as $k=>$v){
            $noticeDetailArray[]=array_merge($arr,['to_user'=>$v,'to_user_name'=>$userArr[$v]??""]);
        }
        $noticeDetail=new NoticeDetail();
        $noticeDetail->insertAll($noticeDetailArray);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){
        error();
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-08 09:09:27
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){

    }
    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data){

    }
}
