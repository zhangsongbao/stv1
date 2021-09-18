<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-23 11:26:36
 */
namespace app\laboratory\controller;
use hnzl\HnzlController;
use think\Db;
use think\Exception;

class Material extends HnzlController{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['selectMaterial'];
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\laboratory\model\Material');
        //载入验证器
        $this->validate=new \app\laboratory\validate\Material();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        //type=1 本站 type=0全部
        $type=$this->request->post('type',1);
        $station=$this->request->post('filter.station_code');
        $where[]=['status','=',0];
        if($type==0){
            //重新定义model 搜查主物料数据
            $model=new \app\laboratory\model\MaterialMaster();
            //重定义where 到主表
            foreach ($where as $k=>$v){
                if(strpos('str-'.$v[0],'station_code')){
                   unset($where[$k]);
                }
            }
            sort($where);

            $model=$model->withCount(["material"=>function($query) use ($station){
                $query->where(['station_code'=>$station]);
            }]);
        }else{
            if(!in_array($station,auth()->stations())){
                //无站点权限  则为非法  默认查不到数据
                success(__( 'Auth Error'));
            }
            $model=$model->with("materialMaster");
        }
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 重写添加
     */
    public function add(){
        $materialCode=$this->request->post('material_code/a',[]);
        $stationCode=$this->request->post('station_code/d','');
        if(!in_array($stationCode,auth()->stations())){
            error(__( 'Auth Error'));
        }
        if(count($materialCode)<1){
            error(__( 'Param Error'));
        }
        //去掉数据库存在的  防止重复
        $isset=$this->model->where([['material_code','in',$materialCode],['station_code','=',$stationCode]])->field("material_code,station_code")->select();
        if($isset){
            foreach ($isset as $k=>$v){
                $key = array_search($v['material_code'], $materialCode);
                if ($key !== false){
                    unset($materialCode[$key]);
                }
            }
        }
        $materialMaster=new \app\laboratory\model\MaterialMaster();
        $inserts=$materialMaster->where([['material_code','in',$materialCode]])
            ->field('material_code,material_name,material_cate_id,status')
            ->select()->toArray();
        array_walk($inserts,function (&$value, $key,$stationCode){
            $value['station_code']=$stationCode;
        },$stationCode);
        $res=$this->model->insertAll($inserts);
        if(!$res){
            error(__( 'Error'));
        }
        redis('hnzl_laboratory_material_sta*',null);
        success(__('Success'));
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        redis('hnzl_laboratory_material_sta*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-23 11:26:36
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }


    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2020/11/19 11:45selectMaterial
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
     public function del(){
         $delete = $this->request->post('material_code/a');
         $stationCode = $this->request->post('station_code/d');
         $where[] = ['material_code', 'in', $delete];
         if(!in_array($stationCode,auth()->stations())){
             error(__( 'Auth Error'));
         }
         $where[]=['station_code','=',$stationCode];
         Db::startTrans();
         try {
             $deleteData = $this->model->where($where)->find();
             if (!$deleteData) {
                 throw new Exception(__("Not Exists"));
             }
             if (!$this->model->where($where)->delete()) {
                 throw new Exception(__("Del Error"));
             }
             redis('hnzl_laboratory_material_sta*',null);
             logs()->log(['deleteData' => $deleteData], 22);
             Db::commit();
         } catch (Exception $e) {
             Db::rollback();
             error($e->getMessage());
         }
         success(__('Del Success'));
     }
    /**
     * @param
     * @author :
     
     * @date: 2020-12-18 15:23:17
     * @name: 选择物料
     */
     public function selectMaterial(){
         $this->index(false);
     }
}
