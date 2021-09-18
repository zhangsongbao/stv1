<?php

namespace app\purchase\validate;
use think\Validate;

class SupplierPrice extends Validate{
    protected $rule =[
        'contract_id'  => 'require',
        'purchase_price'=>'require|float',
        'station_price'=>'require|float',
        'start_time'=>'require|number',
        'end_time'=>'require|number'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['contract_id','purchase_price','station_price','start_time','end_time'],
        'edit'   => ['contract_id','purchase_price','station_price','start_time','end_time'],
    ];
    function __construct()
    {
        $this->message = [
            'contract_id.require' =>  __('Require',['s'=>__('contract_id')]),
            'purchase_price.require' =>  __('Require',['s'=>__('purchase_price')]),
            'station_price.require' =>  __('Require',['s'=>__('station_price')]),
            'start_time.require' =>  __('Require',['s'=>__('start_time')]),
            'end_time.require' =>  __('Require',['s'=>__('end_time')]),
            'purchase_price.float' =>  __('Float',['s'=>__('purchase_price')]),
            'station_price.float' =>  __('Float',['s'=>__('station_price')]),
            'start_time.number' =>  __('Number',['s'=>__('start_time')]),
            'end_time.number' =>  __('Number',['s'=>__('end_time')])

        ];
    }
}