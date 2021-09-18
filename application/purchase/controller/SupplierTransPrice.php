<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 15:23:17
 */

namespace app\purchase\controller;
use app\laboratory\logic\MaterialMaster;
use hnzl\HnzlController;

class SupplierTransPrice extends HnzlController
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
        $this->model = model('app\purchase\model\SupplierTransPrice');
        //载入验证器
        $this->validate = new \app\purchase\validate\SupplierTransPrice();
    }

    /**
     * @author : 萤火虫

     * @date: 2021-3-7 上午10:26
     * @name: 合同列表
     */
    public function ContractList(){
        if ($this->request->isPost()) {
            $type=44;
            $model = new \app\purchase\model\Contract();
            $this->model->station_code='station_code';
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $this->model->station_code=null;
            foreach ($where as $k=>$v) {
                if ($v[0] == 'material_name') {
                    if (!empty($v[2])) {
                        $ids = MaterialMaster::materialCodes($v[2]);
                        $where[]=['material_code','in',$ids];
                    }
                    unset($where[$k]);
                }
            }
            $where=array_merge($where);
            $total = $model
                ->where($where)
                ->count();
            //价格查询最新大于当前时间的价格
            $list = $model->with(['materialMaster'])
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            if($list){
                $list=$list->toArray();
                foreach ($list as $k=>&$v){
                    $v=array_merge($v,$this->model->price($v['id']));
                }
            }

            $result = array("total" => $total, "rows" => $list);

            logs()->log([], $type);
            success(__("Success"), $result);
        }
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where, &$model, $with = true)
    {

       
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result)
    {
    }
    /**
     * @param $insert
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert)
    {
    }
    /**
     * @param $insert
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert)
    {
        $insert['purchase_price']=sprintf ("%.3f", $insert['purchase_price']);;
        $insert['station_price']=sprintf ("%.3f", $insert['station_price']);;
        redis('hnzl_purchase_SupplierTransPrice_'.$insert['contract_id'], $insert);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData)
    {
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData)
    {
        $insert['purchase_price']=sprintf ("%.3f", $updateArr['purchase_price']);;
        $insert['station_price']=sprintf ("%.3f", $updateArr['station_price']);;
        redis('hnzl_purchase_SupplierTransPrice_'.$updateArr['contract_id'], $oldData);
    }

    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where, &$model)
    {
    }
    /**
     * @param $data
     * @author : 

     * @date: 2020-12-18 15:23:17
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data)
    {
    }
    /**
     * @param $data
     * @author : 萤火虫

     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data)
    {
    }
}
