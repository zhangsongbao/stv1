<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-23 09:45:04
 */
class Recipe  extends HnzlModel{
    protected $table = "hnzl_recipe";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    public $station_code='station_code';
    protected $pk='recipe_code';
    public function recipeBase(){
        return $this->hasMany('recipeBase','recipe_code','recipe_code');
    }
    public function materialMaster(){
        return $this->hasOne('materialMaster','material_code','material_code')->bind('material_name');
    }
}