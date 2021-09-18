<?php
/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-13 09:04:07
 */
namespace app\admin\controller;
use hnzl\HnzlController;
use think\Db;
class UserFile extends HnzlController{
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
        $this->model=model('app\admin\model\UserFile');
        //载入验证器
        $this->validate=new \app\admin\validate\UserFile();
    }
    /**
     * @param $where
     * @param $model
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->hasWhere('user',[['is_delete','=',0]])->with('user');
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){

    }
    /**
     * @param $insert
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

    }

    /**
     * @param $where
     * @param $model
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){
        error();
    }
    /**
     * @param $deleteData
     * @author : 萤火虫
     
     * @date: 2020-11-13 09:04:07
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){

    }
    /**
     * @param $data
     * @author : yhc
     
     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data){

    }
}
