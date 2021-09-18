<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-21 14:21:33
 */
namespace app\laboratory\controller;
use hnzl\HnzlController;
class MaterialMaster extends HnzlController{
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
        $this->model=model('app\laboratory\model\MaterialMaster');
        //载入验证器
        $this->validate=new \app\laboratory\validate\MaterialMaster();
    }
    /**
     * @param $where
     * @param $model
     * @author :
     
     * @date: 2020-12-21 14:21:33
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){

    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
          $material = new \app\laboratory\model\Material();
          $material->allowField(true)->save($updateArr,['material_code'=>$updateArr['material_code']]);
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-21 14:21:33
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){
        $material =new \app\laboratory\model\Material();
        $material->where('material_code',$deleteData['material_code'])->delete();
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
