<?php
/**
 * @author : yhc

 * @date: 2020/10/17 上午10:52
 * @name:
 */
namespace app\admin\controller;
use hnzl\HnzlController;
use hnzl\model\AuthGroup;
use think\Exception;

class  AuthRule extends HnzlController
{
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['userMenu'];
    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model=model('hnzl\model\AuthRule');
        //载入验证器
        $this->validate=new \app\admin\validate\AuthRule();
    }
    public function test(){
        var_dump(AuthGroup::getChildrenGroupIds());
    }
    public function userMenu(){
        success(__('Success'),auth()->userMenu());
    }

    /**
     * @param $where
     * @param $model
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name:查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $where[]=['is_show','=',0];
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){

    }
    /**
     * @param $insert
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        redis('Hnzl_Auth*',null);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        redis('Hnzl_Auth*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){
        $issetChild=$this->model->where(['pid'=>$deleteData->id])->value('id');
        if($issetChild){
            throw  new Exception(__('Del',['s'=>__('has child')]));
        }
    }
    /**
     * @param $deleteData
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){
        redis('Hnzl_Auth*',null);
    }
}