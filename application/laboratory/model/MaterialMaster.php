<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-21 14:27:57
 */
class MaterialMaster  extends HnzlModel{
    public $allowField=true;
    public $softDelete=false;
    protected $pk = 'material_code';
    public function material(){
        return $this->hasMany('material','material_code','material_code');
    }

    /**
     * @param $name
     * @return \think\Model[]
     * @author : 萤火虫

     * @date: 2021-3-5 下午2:23
     * @name: 获取物料名称ID
     */
    public static function materialCodes($name){
        return self::where([['material_name','like',$name]])->field('material_code')->select();
    }
}