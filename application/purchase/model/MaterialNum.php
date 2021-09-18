<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-04 16:23:25
 */
class MaterialNum  extends HnzlModel{

    public $allowField=true;
    public $softDelete=false;
    protected $pk='material_code';
    //是否站点权限搜索
    public $station_code='station_code';
    public $allowFieldEdit=['now_num'];
    /**
     * @param $materialCode
     * @author : yhc

     * @date: 2021/03/04 16:27
     * @name: 获取物料编码数量
     */
    public static function getNum($materialCode,$stationCode){
        $num=self::where(['material_code'=>$materialCode,'station_code'=>$stationCode])->value('now_num');

        if(!$num){
            self::insert([
                'material_code'=>$materialCode,
                'station_code'=>$stationCode,
                'create_time'=>time(),
                'update_time'=>time(),
                'now_num'=>0
            ]);
            $num=0;
        }
        return $num??0;
    }

    /**
     * @param $materialCode
     * @param $stationCode
     * @param $num
     * @param int $type
     * @return bool
     * @throws \think\Exception
     * @author : yhc

     * @date: 2021/03/04 16:43
     * @name: 更改库存数量
     */
    public static function changeNum($materialCode,$stationCode,$num,$type=0){
        if($type==0){
            $res=self::where(['material_code'=>$materialCode,'station_code'=>$stationCode])->setInc('now_num',$num);
        }else{
            $res=self::where(['material_code'=>$materialCode,'station_code'=>$stationCode])->setDec('now_num',$num);
        }

        return $res;
    }
    public function materialMaster(){
        return $this->hasOne('\app\laboratory\model\MaterialMaster','material_code','material_code')->bind('material_name');
    }
}