<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\purchase\validate;

use think\Validate;

class SupplierDispatch extends Validate
{
    protected $rule = [
        'contract_id' => 'integer',
        'material_code' => 'integer',
        'material_name' => 'require',
        'supplier_name' => 'require',
        'purchase_station_code' => 'integer|require',
        'receive_station_code' => 'integer|require',
        'unit' => 'require|length:0,20',
        'bill' => 'integer',
        'supplier_time' => 'require|number',
        'supplier_weight' => 'require|float',
        'car_plate' => 'require',
        'attach_id' => 'integer',
    ];

    protected $message = null;
    protected $scene = [
        'add' => ['contract_id', 'material_code', 'material_name', 'supplier_name', 'purchase_station_code', 
        'receive_station_code', 'unit', 'bill', 'supplier_time', 'supplier_weight', 'car_plate', 'attach_id'],
        'edit' => ['contract_id', 'material_code', 'material_name', 'supplier_name', 'purchase_station_code',
            'receive_station_code', 'unit', 'bill', 'supplier_time', 'supplier_weight', 'car_plate',  'attach_id',],
    ];

    function __construct()
    {
        $this->message = [
            'contract_id.integer' => __('Integer', ['s' => __('contract_id')]),
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),

            'supplier_name.require' => __('Require', ['s' => __('supplier_name')]),
            'purchase_station_code.integer' => __('Integer', ['s' => __('purchase_station_code')]),
            'receive_station_code.integer' => __('Integer', ['s' => __('receive_station_code')]),
                'purchase_station_code.require' => __('Require', ['s' => __('purchase_station_code')]),
            'receive_station_code.require' => __('Require', ['s' => __('receive_station_code')]),
            'unit.require' => __('Require', ['s' => __('unit')]),
            'unit.length' => __('Length', ['s' => __('unit')]),
            'bill.integer' => __('Integer', ['s' => __('bill')]),
            'supplier_time.require' => __('Require', ['s' => __('supplier_time')]),
            'supplier_time.number' => __('Number', ['s' => __('supplier_time')]),
            'supplier_weight.require' => __('Require', ['s' => __('supplier_weight')]),
            'supplier_weight.float' => __('Float', ['s' => __('supplier_weight')]),
            'car_plate.require' => __('Require', ['s' => __('car_plate')]),

            'attach_id.integer' => __('Integer', ['s' => __('attach_id')]),
          
        ];
    }
}