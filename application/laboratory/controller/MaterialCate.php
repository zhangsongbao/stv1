<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-21 10:26:35
 */
namespace app\laboratory\controller;
use hnzl\HnzlController;
use think\Db;

class MaterialCate extends HnzlController{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['getCate'];
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\laboratory\model\MaterialCate');
        //载入验证器
        $this->validate=new \app\laboratory\validate\MaterialCate();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->withCount(["materialMaster"]);
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        if($this->model->find($insert['material_cate_id'])){
            error(__('Unique', ['s' => __('material_cate_id')]));
        }
    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        redis('hnzl_laboratory_materialCate*',null);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        if($updateArr['status']!=$oldData['status']){
            $material_master = new \app\laboratory\model\MaterialMaster();
            $material = new \app\laboratory\model\Material();
            $where=['material_cate_id'=>$updateArr['material_cate_id']];
            $update=['status'=>$updateArr['status']];
            $material_master->where($where)->update($update);
            $material->where($where)->update($update);
        }
        redis('hnzl_laboratory_materialCate*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-21 10:26:35
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){
        $material_master = model('app\laboratory\model\MaterialMaster');
        $material_master->where('material_cate_id',$deleteData['material_cate_id'])->delete();
        $material = new \app\laboratory\model\Material();
        $material->where('material_cate_id',$deleteData['material_cate_id'])->delete();
        redis('hnzl_laboratory_materialCate*',null);
        success(__('Del Success'));
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
     * @author : yhc
     
     * @date: 2020/12/23 14:42
     * @name: 单独修改分类状态
     */
     public function status(){
         $status=$this->request->post('status/d');
         $cateId=$this->request->post('material_cate_id/d');
         if(is_null($status)|| !$cateId){
             error(__('Params Error'));
         }
         $where[]=["material_cate_id",'=',$cateId];
         $update['status']=$status;
         Db::startTrans();
         try{
             $oldData['status']=$this->model->where($where)->value("status");
             $update['update_time']=time();
             $this->model->save($update,$where);
             $update['material_cate_id']=$cateId;
             $this->_end_edit($update,$oldData);
             logs()->log(['oldData' => $oldData, 'newData' => $update], 3);
             redis('hnzl_laboratory_materialCate*',null);
             Db::commit();
         }catch (\Exception $e){
             Db::rollback();
             error(__('Edit Error'));
         }
         success(__('Edit Success'));
     }

    /**
     * @author : yhc
     
     * @date: 2020/12/23 14:42
     * @name: 获取所有分类
     */
     public function  getCate(){
         $stationCode=$this->request->post('station_code');
         if($stationCode){
             if(!in_array($stationCode,array_keys(auth()->stations()))){
                 //无站点权限  则为非法  默认查不到数据
                 success(__('Success'));
             }
             $cateIds=\app\laboratory\model\Material::getSationCateIds($stationCode);
             success(__('Success'),$this->model->getCate([['material_cate_id','in',$cateIds]]));
         }else{
             success(__('Success'),$this->model->getCate());
         }

     }
}
