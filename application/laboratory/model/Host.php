<?php
namespace app\laboratory\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 15:23:17
 */
class Host  extends HnzlModel{
    protected $table = "hnzl_host";
    public $allowField=true;
    public $softDelete=false;
    protected $pk='id';
    public $station_code='station_code';
    public function hostSilo(){
        return $this->hasMany('HostSilo','host_code','host_code');
    }
    public static function stationHost($stationCode,$field='host_code,host_name'){
        $cacheKey='hnzl_laboratory_host-'.$stationCode.$field;
        if(!$stationHost=redis($cacheKey)){
            $stationHost=self::where('station_code',$stationCode)->column($field);
            redis($cacheKey,$stationHost);
        }
        return $stationHost;
    }
}