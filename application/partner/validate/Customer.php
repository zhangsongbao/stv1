<?php
/**
 * @author : slh
 * @date: 2021/07/23
 * @name:
 */
namespace app\partner\validate;
use think\Validate;
class Customer extends Validate
{
    protected $rule =[
        'station_code' => 'require',
        'partner_id'   => 'require|unique:customer,partner_id^station_code'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['station_code','partner_id'],
        'edit'  => ['station_code','partner_id']
    ];
    function __construct()
    {
        $this->message = [
            'station_code.require' =>  __('Require',['s'=>__('station_code')]),
            'partner_id.require' =>  __('Require',['s'=>__('partner_id')]),
            'partner_id.unique' =>  __('Unique',['s'=>__('partner_id')]),
        ];
    }
}