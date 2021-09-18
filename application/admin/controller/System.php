<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-25 11:57:33
 */
namespace app\admin\controller;
use hnzl\HnzlController;
class System extends HnzlController{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['systems'];
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\admin\model\System');
        //载入验证器
        $this->validate=new \app\admin\validate\System();
    }
    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){

    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        //验证二级
        if($insert['pid']!=0){
            $parent=$this->model->parent($insert['pid'],'id,pid');
            if(!$parent){
                error(__('pid').__('Not Exists'));
            }
            if($parent['pid']!==0){
                error(__('pid not allow'));
            }
        }
    }
    /**
     * @param $insert
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){
        //验证二级
        if($updateArr['pid']!=$oldData['pid']){
            $parent=$this->model->parent($updateArr['pid'],'id,pid');
            if(!$parent){
                error(__('pid').__('Not Exists'));
            }
            if($parent['pid']!==0){
                error(__('pid not allow'));
            }
        }
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

    }

    /**
     * @param $where
     * @param $model
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-25 11:57:33
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){
        //验证二级
        if($deleteData['pid']==0){
            $child=$this->model->where([
                'pid'=>['=',$deleteData['id']],
                'is_delete'=>['=',0]
            ])->value('id');
            if($child){
                error(__('has child'));
            }
        }
    }
    /**
     * @param $deleteData
     * @author : 

     * @date: 2021-03-25 11:57:33
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
    protected function _before_import(&$data){

    }
    /**
     * @param $deleteData
     * @author :

     * @date: 2020-12-18 15:23:17
     * @name: 删除后置
     */
     public function _before_delAll(&$deleteData){
            error();
     }

    /**
     * @author : 萤火虫

     * @date: 2021-3-25 下午4:48
     * @name: 获取配置
     */
     public function systems(){
         list($where, $sort, $order, $offset, $limit) = $this->buildparams();
         buildWhere($where);
         success('',$this->model->where($where)->select());
     }
}
