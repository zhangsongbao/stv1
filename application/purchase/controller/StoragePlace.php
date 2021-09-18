<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 15:23:17
 */
namespace app\purchase\controller;
use hnzl\HnzlController;
class StoragePlace extends HnzlController{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['select'];
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\purchase\model\StoragePlace');
        //载入验证器
        $this->validate=new \app\purchase\validate\StoragePlace();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
       
        $model=$model->with('station');
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        redis('hnzl_purchase_storagePlace*',null);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

        redis('hnzl_purchase_storagePlace*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

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
     * @author : 萤火虫

     * @date: 2021-3-8 下午4:38
     * @name: 选择存放地
     */
    public function select(){
        $this->index(false);
    }

   
}
