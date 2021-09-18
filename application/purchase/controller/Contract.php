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

class Contract extends HnzlController{
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
        $this->model=model('app\purchase\model\Contract');
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
        //主合同 采购站点为外部供应商 不做限制
        $insert['pid']=$insert['pid']??0;
        if($insert['pid']==0){
            $authStation=$insert['station_code'];
        }else{
            //子合同采购站点为自己管理站点 收货站点不限制
            $where=[
                ['pid','=',$insert['pid']]
            ];
            //查找父合同
            $parentContract=$this->model->field('supplier_id,supplier_name,material_code,
            station_code as supplier_id,unit')->where($where)->find();
            //限制采购为自己站点
            $authStation=$insert['supplier_id'];
            if(!$parentContract){
                error(__('Error'));
            }
            $insert=array_merge($insert,$parentContract->toArray());
        }
        if(!in_array($authStation,auth()->stations())  ){
            error(__('Auth Error'));
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
        //增加限制采购站点必须为自己管理站点
        //主合同 采购站点为外部供应商 不做限制 //子合同采购站点为自己管理站点 收货站点不限制
        $updateArr['pid']=$updateArr['pid']??0;
        if($updateArr['pid']!=$oldData['pid']){
            //子合同不允许修改上级
            error(__('pid not allow'));
        }
        if($updateArr['pid']==0){
            //上级合同修改 下级合同跟随
            $childUpdate=$updateArr;
            unset($childUpdate['supplier_id_select'],$childUpdate['supplier_id_select']);
            $this->model->isUpdate(true)->save($childUpdate,[['pid','=',$updateArr['id']]]);
        }else{
            //下级合同只允许修改收货站点
            $updateArr=[
                'station_code'=>$updateArr['station_code']
            ];
        }
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2021-03-04 11:26:10
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

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

    /**
     * @author : 萤火虫
     
     * @date: 2021-4-6 上午9:20
     * @name: 选择合同
     */
     public function select(){
         $this->index(false);
     }

}
