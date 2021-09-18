<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-05 16:59:25
 */
class Purchase  extends HnzlModel{
    protected $table = "hnzl_purchase";
    public $allowField=true;
    public $softDelete=false;
    public $searchField=[
        'code','id','contract_id','material_code','purchase_station_code','receive_station_code','supplier_name','supplier_time','supplier_id',
        'material_name','create_time','way_in_time','way_out_time','supplier_dispatch_id','car_plate','storage_place_id'
    ];
    //是否站点权限搜索
    public $station_code='receive_station_code';
    //public $allowFieldAdd=['silo_code','silo_name','host_code','station_code','field_silo','field_recipe','field_cost'];
    //public $allowFieldEdit=true;
    public function  storagePlace(){
        return $this->hasOne('StoragePlace','id','storage_place_id')->bind(['storage_place_name'=>'name']);
    }

}