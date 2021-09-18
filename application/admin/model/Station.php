<?php
namespace app\admin\model;
use hnzl\HnzlModel;

/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-06 16:52:38
 */
class Station  extends HnzlModel{
    public $allowField=true;
    public $softDelete='is_delete';
    public function title()
    {
        return $this->hasMany('app\admin\model\Config','name','id');
    }
    public function station(){
        return $this->hasMany('app\admin\model\UserStation','user_station_id','value');
    }
    /**
     * @param null $userId
     * @return mixed|\think\Model[]
     * @author : yhc

     * @date: 2020/11/02 15:37
     * @name: 获取站点key/value
     */
    public static function stations($field='value,name',$userId=null){
        $cacheKey='hnzl_stationALl'.md5($field.$userId);

        if(!$userStations=redis($cacheKey)){
            if($userId){
                $stationIds=UserStation::where('user_id',$userId)->column('user_station_id');
                $userStations=self::where('value','in',$stationIds)->column($field);
            }else{
                $userStations=self::column($field);
            }
            redis($cacheKey,$userStations);
        }
        return $userStations;
    }

}