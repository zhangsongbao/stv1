<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-24 09:24:43
 */
class RecipeMaterialBase  extends HnzlModel{
    protected $table = "hnzl_recipe_material_base";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    public $station_code=null;
    protected $pk='rmb_id';
    public function rmbDetail(){
        return $this->hasMany('rmbDetail','rmb_id','rmb_id');
    }
    public function user(){
        return $this->hasOne('hnzl\model\User','id','update_user')->field('id,user_name,real_name');
    }
}