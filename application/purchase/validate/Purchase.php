<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\purchase\validate;

use think\Validate;

class Purchase extends Validate
{
    protected $rule = [
        'contract_id' => 'integer',
        'supplier_dispatch_id' => 'integer',
        'material_code' => 'integer|require',
        'supplier_name' => 'require',
        'supplier_id_select' => 'integer|require',
        'station_code' => 'integer|require',
        'unit' => 'require',
        'bill' => 'integer',
        'supplier_time' => 'integer',
        'supplier_weight' => 'require',
        'car_plate' => 'require',
        'attach_id' => 'integer',
        'gross_weight' => 'float',
        'tare weight' => 'float',
        'deduct_percent' => 'integer',
        'deduct_num' => 'float',
        'cal_weight' => 'float',
        'convert_num' => 'float',
        'convert_weight' => 'float',
        'note' => 'length:0,200',
        'storage_place_id' => 'require|integer',
        'purchase_price' => 'require',
        'station_price' => 'require',
        'trans_price' => 'require',
        'trans_money' => 'require',
        'station_money' => 'require',
        'purchase_money' => 'require',
        'trans_purchase_price' => 'require',
        'trans_purchase_money' => 'require',
        'status' => 'integer',

    ];

    protected $message = null;
    protected $scene = [
        'add' => ['contract_id', 'supplier_dispatch_id', 'material_code', 'supplier_name',
            'station_code', 'receive_station_code', 'unit', 'bill', 'supplier_time', 'supplier_weight', 'car_plate',
            'attach_id', 'gross_weight', 'tare weight', 'deduct_percent', 'deduct_num', 'cal_weight',
            'convert_num', 'storage_place_id'],
        'edit' => [ 'contract_id', 'supplier_dispatch_id', 'material_code', 'supplier_name',
            'station_code', 'receive_station_code', 'unit', 'bill', 'supplier_time', 'supplier_weight', 'car_plate',
            'attach_id', 'gross_weight', 'tare weight',  'deduct_percent', 'deduct_num', 'cal_weight',
            'convert_num', 'storage_place_id', ],
    ];

    function __construct()
    {
        $this->message = [
        
            'contract_id.integer' => __('Integer', ['s' => __('contract_id')]),
            'supplier_dispatch_id.integer' => __('Integer', ['s' => __('supplier_dispatch_id')]),
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),
            'supplier_name.require' => __('Require', ['s' => __('supplier_name')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'station_code.integer' => __('Integer', ['s' => __('station_code')]),
            'receive_station_code.integer' => __('Integer', ['s' => __('receive_station_code')]),
            'unit.require' => __('Require', ['s' => __('unit')]),
            'purchase_station_code.require' => __('Require', ['s' => __('purchase_station_code')]),
            'receive_station_code.require' => __('Require', ['s' => __('receive_station_code')]),
            'bill.integer' => __('Integer', ['s' => __('bill')]),
            'supplier_time.integer' => __('Integer', ['s' => __('supplier_time')]),
            'supplier_weight.require' => __('Require', ['s' => __('supplier_weight')]),
            'car_plate.require' => __('Require', ['s' => __('car_plate')]),
            'attach_id.integer' => __('Integer', ['s' => __('attach_id')]),
            'gross_weight.float' => __('Float', ['s' => __('gross_weight')]),
            'tare_weight.float' => __('Float', ['s' => __('tare_weight')]),

            'deduct_percent.integer' => __('Integer', ['s' => __('deduct_percent')]),
            'deduct_num.float' => __('Float', ['s' => __('deduct_num')]),
            'cal_weight.float' => __('Float', ['s' => __('cal_weight')]),
            'convert_num.float' => __('Float', ['s' => __('convert_num')]),

            'note.length' => __('Length', ['s' => __('note')]),
            'storage_place_id.integer' => __('Integer', ['s' => __('storage_place_id')]),
            'storage_place_id.require' => __('Require', ['s' => __('storage_place_id')]),
            'purchase_price.require' => __('Require', ['s' => __('purchase_price')]),
            'station_price.require' => __('Require', ['s' => __('station_price')]),
            'trans_price.require' => __('Require', ['s' => __('trans_price')]),
            'trans_money.require' => __('Require', ['s' => __('trans_money')]),
            'station_money.require' => __('Require', ['s' => __('station_money')]),
            'purchase_money.require' => __('Require', ['s' => __('purchase_money')]),
            'trans_purchase_price.require' => __('Require', ['s' => __('trans_purchase_price')]),
            'trans_purchase_money.require' => __('Require', ['s' => __('trans_purchase_money')]),
            'status.integer' => __('Integer', ['s' => __('status')]),

        ];
    }
}