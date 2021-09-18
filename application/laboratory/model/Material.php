<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-21 15:40:04
 */
class Material  extends HnzlModel{

    public $allowField=true;
    public $softDelete=false;
    protected $pk = 'material_code';
    public $station_code='station_code';
    public function materialMaster(){
        return $this->hasOne('materialMaster','material_code','material_code')->bind([
            'sort',
            'unit',
            'unit_desc',
            'coefficient',
            'balance_unit',
            'balance_unit_desc',
            'cal_coefficient',
            'desc',
        ]);
    }
    public static function getSationCateIds($stationCode){
        $cacheKey='hnzl_laboratory_material_sta'.$stationCode;
        if(!$getSationCateIds=redis($cacheKey)){
            $getSationCateIds=self::where(['stationCode'=>$stationCode,'status'=>0])->column('material_cate_id');
            redis($cacheKey,$getSationCateIds);
        }
        return $getSationCateIds;
    }
}