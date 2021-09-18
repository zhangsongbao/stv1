<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-04 11:26:10
 */
class Contract  extends HnzlModel{
    protected $table = "hnzl_contract";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    //默认限制收货站点
    public $station_code='station_code';
    //public $allowFieldAdd=['silo_code','silo_name','host_code','station_code','field_silo','field_recipe','field_cost'];
    //public $allowFieldEdit=true;
    public function materialMaster(){
        return $this->hasOne('\app\laboratory\model\MaterialMaster','material_code','material_code')->bind('material_name');
    }
    public function supplierPrice(){
        return $this->hasOne('\app\purchase\model\SupplierPrice','contract_id','id')->bind('purchase_price,station_price');
    }
    public function supplierTransPrice(){
        return $this->hasOne('\app\purchase\model\SupplierTransPrice','contract_id','id')->bind('purchase_price,station_price');
    }



}