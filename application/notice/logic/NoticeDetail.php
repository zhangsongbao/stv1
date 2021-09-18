<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/12/01 08:56
 * @name:
 */
namespace  app\notice\logic;
use app\notice\model\NoticeDetail as Nd;
class  NoticeDetail {
    protected $model;

    public function __construct()
    {
        $this->model=new Nd();
    }

    /**
     * @param $where
     * @return \think\Model[]
     * @author : yhc

     * @date: 2020/12/01 09:09
     * @name: 获取所有通知
     */
    public  function getNoticeAll($where,$delay=false){
        //间隔5分钟通知
        $time=time();
        $whereBase[]=['up_time','>',$time-$delay];
        $whereBase[]=['up_time','<',$time];
        $res=$this->model->where($where)->where($whereBase)->select();
        //更新时间
        if($delay){
            $delay=(int)$delay;
             $this->model->where($where)->where($whereBase)->update(['up_time'=>$time+$delay]);
        }
        if($res){
            $res=$res->toArray();
        }
        return $res;
    }

    /**
     * @param $id
     * @author : yhc

     * @date: 2020/12/01 09:09
     * @name: 设置已读
     */
    public function setRead($id){

    }
    public function delayALL($where){
        return $this->model->where($where)->update(['up_time']);
    }
}