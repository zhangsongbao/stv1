<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 15:23:17
 */
namespace app\purchase\controller;
use hnzl\HnzlController;
use think\Db;
class Supplier extends HnzlController{
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
        $this->model=model('app\purchase\model\Supplier');
        //载入验证器
        $this->validate=new \app\purchase\validate\Supplier();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        if($with==true){
            $model=$model->with('attachment');
        }
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
        redis('hnzl_purchase_supplier*',null);
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
        redis('hnzl_purchase_supplier*',null);
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
     * @author : slh
     
     * @date: 2021/3/4
     * @name: 单独修改供应商状态
     */
    public function status(){
        $status=$this->request->post('status');
        $supplierId=$this->request->post('id/d');
        if(is_null($status)|| !$supplierId){
            error(__('Params Error'));
        }
        $where[]=["id",'=',$supplierId];
        $update['status']=$status;
        Db::startTrans();
        try{
            $oldData['status']=$this->model->where($where)->value("status");
            $update['update_time']=time();
            $this->model->save($update,$where);
            $update['material_cate_id']=$supplierId;
            $this->_end_edit($update,$oldData);
            logs()->log(['oldData' => $oldData, 'newData' => $update], 3);
            redis('hnzl_purchase_supplier*',null);
            Db::commit();
        }catch (\Exception $e){
            Db::rollback();
            error(__('Edit Error'));
        }
        success(__('Edit Success'));
    }

    /**
     * @author : 萤火虫
     
     * @date: 2021-3-8 下午4:36
     * @name: 选择供应商接口
     */
    public function select(){
        $this->index(false,false);
    }

   
}
