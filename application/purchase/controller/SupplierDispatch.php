<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-08 11:20:41
 */
namespace app\purchase\controller;
use hnzl\HnzlController;
class SupplierDispatch extends HnzlController{
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
        $this->model=model('app\purchase\model\SupplierDispatch');
        //载入验证器
        $this->validate=new \app\purchase\validate\SupplierDispatch();
    }
    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->with('attachment');
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $lastCode=$this->model->where([
            ['receive_station_code','=',(int)$insert['receive_station_code']],
            ['create_time','>',strtotime(date('Y-m-d 00:00:00'))]
        ])->value("code");
        $lastCode=$lastCode??0;
        $num=sprintf ("%04d", substr($lastCode,-4)+1);
        $insert['code']=$insert['receive_station_code'].date('Ymd').$num;

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){
        //状态不为0 即为到达状态 禁止修改派车单
        if($oldData['status']!=0){
            error(__('status_forbid'));
        }
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

    }

    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-08 11:20:41
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-08 11:20:41
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
    /**
     * @param $deleteData
     * @author :

     * @date: 2020-12-18 15:23:17
     * @name: 删除后置
     */
     public function _before_delAll(&$deleteData){

     }
}
