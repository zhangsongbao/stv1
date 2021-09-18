<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 17:01:12
 */
class HostSilo  extends HnzlModel{
    public $allowFieldAdd=['silo_code','silo_name','host_code','station_code','field_silo','field_recipe','field_cost'];
    public $allowFieldEdit=['silo_name','field_silo','field_recipe','field_cost'];
    public $allowField=true;
    public $softDelete=false;
    protected $pk='id';
    public $station_code='station_code';
    public static function getSiloMarId($host,$station){
        $cacheKey='hnzl_laboratory_hostSilo-'.$host.'-'.$station;
        if(!$SiloMarId=redis($cacheKey)){
            $where=[
                ['host_code','=',$host],
                ['station_code','=',$station]
            ];
            $hostSilo=self::where($where)->field('silo_code,material_code')->select();
            $siloMar=self::getSiloMarStr($hostSilo->toArray());

            $where[]=['silo_mar','=',$siloMar['silo_mar']];
            $SiloMarId=SiloMar::where($where)->value('silo_mar_id');
            redis($cacheKey,$SiloMarId);
        }
        return $SiloMarId;
    }
    public static function getSiloMarStr($details,$type=0){
        $sortArray=arraySort($details,'silo_code',$sort=SORT_ASC);
        $return=[
            'silo_mar'=>'',
        ];
        if($type==0){
            foreach ($sortArray as $k=>$v){
                $return['silo_mar'].=$v['silo_code'].'-'.$v['material_code'].'~';
            }
            return $return;
        }
        $return['silo_mar_str']='';
        foreach ($sortArray as $k=>$v){
            $return['silo_mar'].=$v['silo_code'].'-'.$v['material_code'].'~';
            $return['silo_mar_str'].=$v['silo_code'].'-'.$v['material_name'].'~';
        }
        return $return;
    }
    public function getSilo($stationCode,$hostCode){
        $where=[
            ['station_code','=',$stationCode],
            ['host_code','=',$hostCode]
        ];
        return self::where($where)->field('silo_code,silo_name,material_code,material_name')->select();
    }
}