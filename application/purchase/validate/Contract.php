<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\purchase\validate;
use think\Validate;
class Contract extends Validate
{
    protected $rule =[
        'supplier_id'  => 'require|integer',
        'material_code'  => 'require|integer',
        'station_code'  => 'require|integer',
        'supplier_id_select'  => 'require|integer',
        'unit'  => 'require',
        'pid'  => 'require|integer',
        'status'  => 'integer',

    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['supplier_id','material_code','supplier_id_select','station_code','unit','status','pid',],
        'edit'  => ['station_code','pid'],
    ];
    function __construct()
    {
        $this->message = [
            'supplier_id.integer' =>  __('Integer',['s'=>__('supplier_id')]),
            'material_code.integer' =>  __('Integer',['s'=>__('material_code')]),
            'supplier_id_select.require' =>  __('Require',['s'=>__('supplier_id_select')]),
            'supplier_id_select.integer' =>  __('Integer',['s'=>__('supplier_id_select')]),
            'unit.require' =>  __('Require',['s'=>__('unit')]),
            'status.integer' =>  __('Integer',['s'=>__('status')]),
            'material_code.require' =>  __('Require',['s'=>__('material_code')]),
            'pid.require' =>  __('Require',['s'=>__('pid')]),
            'pid.integer' =>  __('Integer',['s'=>__('pid')]),
            'supplier_id.require' =>  __('Require',['s'=>__('supplier_id')]),
            'station_code.require' =>  __('Require',['s'=>__('station_code')]),
            'station_code.integer' =>  __('Integer',['s'=>__('station_code')]),
        ];
    }
}