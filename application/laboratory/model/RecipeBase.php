<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-23 10:19:01
 */
class RecipeBase  extends HnzlModel{
    protected $table = "hnzl_recipe_base";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    public $station_code=null;
    protected $pk='recipe_code';
}