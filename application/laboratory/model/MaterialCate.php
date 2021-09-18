<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-21 10:26:35
 */
class MaterialCate  extends HnzlModel{

    public $allowField=true;
    public $softDelete=false;
    protected $pk='material_cate_id';
    public function getCate($where=['status'=>0],$field='material_cate_id,cate_name'){
        $cacheKey='hnzl_laboratory_materialCate'.md5(json_encode($where,true),$field);
        if(!$cate=redis($cacheKey)){
            $cate=self::where($where)->column($field);
            redis($cacheKey,$cate);
        }
        return $cate;
    }
    public function materialMaster(){
        return $this->hasMany('materialMaster','material_cate_id','material_cate_id');
    }
}