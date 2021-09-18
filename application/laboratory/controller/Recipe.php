<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-23 09:11:43
 */
namespace app\laboratory\controller;
use app\laboratory\model\RecipeBase;
use hnzl\HnzlController;
use think\Db;

class Recipe extends HnzlController{
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
        $this->model=model('app\laboratory\model\Recipe');
        //载入验证器
        $this->validate=new \app\laboratory\validate\Recipe();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->with(['recipeBase','materialMaster']);
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $this->LoadLang('RecipeBase');
        $validate = new \app\laboratory\validate\RecipeBase();
        foreach ($insert['recipe_base'] as $k=>$v){
            if (!$validate->scene('add')->check($v)) {
                error($validate->getError(), '', 2);
            }
        }
    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        $RecipeBase=new RecipeBase();
        $id=$insert['id'];
        array_walk($insert['recipe_base'],function (&$value, $key,$id){
            $value['recipe_code']=$id;
        },$id);
        $allField=$RecipeBase->allowFieldAdd??$RecipeBase->allowField;
        $res=$RecipeBase->allowField($allField)->insertAll($insert['recipe_base']);
        if(!$res){
            error('material add error');
        }
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        $RecipeBase=new RecipeBase();
        $id=$oldData['recipe_code'];
        array_walk($updateArr['recipe_base'],function (&$value, $key,$id){
            $value['recipe_code']=$id;
        },$id);
        $this->LoadLang('RecipeBase');
        $validate = new \app\laboratory\validate\RecipeBase();
        foreach ($updateArr['recipe_base'] as $k=>$v){
            if (!$validate->check($v)) {
                error($validate->getError(), '', 2);
            }
        }
        $allField=$RecipeBase->allowFieldEdit??$RecipeBase->allowField;
        $RecipeBase->where(['recipe_code'=>$id])->delete();
        $res=$RecipeBase->allowField($allField)->insertAll($updateArr['recipe_base']);
        if(!$res){
            error('material add error');
        }
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-23 09:11:43
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-23 09:11:43
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
     * @author : yhc
     
     * @date: 2020/12/31 11:20
     * @name: 选择基础配比
     */
     public function  select(){
         $this->index(false);
     }
}
