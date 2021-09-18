<?php

namespace app\purchase\model;

use hnzl\HnzlModel;

class SupplierPrice extends HnzlModel
{
    protected $table = "hnzl_supplier_price";
    public $allowField = true;
    public $softDelete = false;
    protected $pk = 'id';
    public $station_code=null;
    /**
     * @param $contract_id
     * @return SupplierPrice|bool|mixed
     * @author : 萤火虫

     * @date: 2021-3-7 上午10:18
     * @name: 获取当前价格
     */
    public  function price($contract_id){
        redis('hnzl_purchase_supplierPrice*', null);
        $CacheKey='hnzl_purchase_supplierPrice_'.$contract_id;
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
