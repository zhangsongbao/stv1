<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-04 11:26:10
 */
namespace app\purchase\controller;


use app\laboratory\logic\MaterialMaster;
use hnzl\HnzlController;
use think\Exception;
//采购合同控制器
class PurchaseContract extends HnzlController{
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
        $this->model=model('app\purchase\model\Contract');
        $this->model->station_code='purchase_station_code';
        //载入验证器
        $this->validate=new \app\purchase\validate\Contract();
    }
    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        foreach ($where as $k=>$v) {
            if ($v[0] == 'material_name') {
                if (!empty($v[2])) {
                    $ids = MaterialMaster::materialCodes($v[2]);
                    if($ids){
                        $where[]=['material_code','in',$ids];
                    }else{
                        $where[]=['material_code','=',0];
                    }
                }
                unset($where[$k]);
            }
        }
        $where=array_merge($where);
        $model=$model->with('materialMaster');
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $insert['supplier_name']=\app\purchase\model\Supplier::getSupplierName($insert['supplier_id']);
        if(!$insert['supplier_name']){
            error( __('Err',['s'=>__('supplier_id')]));
        }
    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        //检验站点权限  限制采购站点
        if(!in_array($oldData['purchase_station_code'],auth()->stations()) ){
            error(__('Auth Error'));
        }

    }

    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-04 11:26:10
     * @name: 删除前置 禁止删除 直接error
     */
    public function _before_del(&$deleteData){
        error();
    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-04 11:26:10
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
     public function select(){
         $this->index(false);
     }
}
