<?php

namespace app\purchase\model;

use hnzl\HnzlModel;

class SupplierTransPrice extends HnzlModel
{
    protected $table = "hnzl_supplier_trans_price";
    public $allowField = true;
    public $softDelete = false;
    protected $pk = 'id';
    public $station_code=null;
    public  function price($contract_id){
        redis('hnzl_purchase_supplierPrice*', null);
        $CacheKey='hnzl_purchase_SupplierTransPrice_'.$contract_id;
        if(!$res=redis($CacheKey)){
            $where=[
                ['start_time','<',time()],
                ['end_time','>',time()],
                ['contract_id','=',$contract_id]
            ];
            $res=$this->where($where)->order('id desc')->field('purchase_price,station_price,start_time,end_time,cal')->find();
            if($res){
                $res=$res->toArray();
            }else{
                $res=[];
            }
            redis($CacheKey,$res);
        }
        return $res;
    }
}
