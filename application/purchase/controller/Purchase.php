<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-05 16:59:25
 */
namespace app\purchase\controller;
use app\laboratory\logic\MaterialMaster;
use hnzl\HnzlController;
class Purchase extends HnzlController{
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
        $this->model=model('app\purchase\model\Purchase');
        //载入验证器
        $this->validate=new \app\purchase\validate\Purchase();
    }
    public function _before_index(&$where,&$model,$with){
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
        $model=$model->with('storagePlace');
    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2021-03-05 16:59:25
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $insert['status']=1;
        $insert['receive_station_code']=$insert['receive_station_code']??"";
        $insert['code']=\app\purchase\logic\Purchase::code($insert['receive_station_code']);
        $insert['net_weight']=$insert['gross_weight']-$insert['tare_weight'];
        $insert['cal_weight']=$insert['net_weight']*(1-$insert['deduct_percent']/100) - $insert['deduct_num'];
        $insert['convert_num']=$insert['convert_num']??1000;
        $insert['convert_weight']=$insert['convert_weight']??$insert['cal_weight']/$insert['convert_num'];
    }
    public function _before_edit(&$updateArr){
        $updateArr['net_weight']=$updateArr['gross_weight']-$updateArr['tare_weight'];
        $updateArr['cal_weight']=$updateArr['net_weight']*(1-$updateArr['deduct_percent']/100) - $updateArr['deduct_num'];
        $updateArr['convert_num']=$updateArr['convert_num']??1000;
        $updateArr['convert_weight']=$updateArr['convert_weight']??$updateArr['cal_weight']/$updateArr['convert_num'];
    }

    /**
     * @author : 萤火虫
     
     * @date: 2021-3-8 上午11:01
     * @name: 状态修改
     */
    public function status(){
        if($this->request->isPost()) {
            $status = $this->request->post('status/d');
            if ($status > 10) {
                error();
            }
            $where = ['id' => $this->request->post('id/d')];
            $old=$this->model->where($where)->find();
            if($status==$old['status']){
                error(__('Not change'));
            }
            $res = $this->model->allowField(['status'])->save(['status' => $status], $where);
            if($res){
                logs()->log([
                        'oldData' => $old,
                        'newStatus' =>array_merge($old->toArray(),['status' => $status])]
                    , 3);
                success(__("Success"));
            }
            error(__('Edit error'));
        }
    }
    public function report(){
        if ($this->request->isPost()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $model = $this->model;
            $groupBy=$this->request->post("group/a");

            if(!$groupBy){
                $groupBy=[];
            }
            $where[]=['status','=',1];
            if(count($groupBy)>0){
                $field=[
                    'material_code','material_name','supplier_name','storage_place_id','car_plate','contract_id',
                    'storage_place_id','sum(supplier_weight) as supplier_weight',
                    'sum(cal_weight) as cal_weight','sum(convert_weight) as convert_weight','count(id) as car_num',
                    "group_concat((case  when note!='' then concat(code,'-',note) else ' ' end ) SEPARATOR '') as note"
                ];
                $total = $model
                    ->where($where)->field($field)
                    ->group($groupBy)
                    ->count();
                $list = $model->with('storagePlace')
                    ->where($where)->field($field)->order($sort, $order)->limit($offset, $limit)
                    ->group($groupBy)
                    ->select();
            }else{
                $total = $model->where($where)
                    ->count();
                $list = $model->with('storagePlace')
                    ->where($where)->order($sort, $order)->limit($offset, $limit)
                    ->select();
            }
            $result = array("total" => $total, "rows" => $list);

            logs()->log([], 44);
            success(__("Success"), $result);
        }
    }

}
