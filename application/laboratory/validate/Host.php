<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\laboratory\validate;
use think\Validate;
class Host extends Validate
{
    protected $rule =[
        'host_code'  => 'integer',
        'host_name'  => 'require',
        'host_ip'  => 'require',
        'station_code'  => 'integer|require',
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['host_name','host_ip','station_code',],
        'edit'   => ['host_name','host_ip','station_code',],
    ];
    function __construct()
    {
        $this->message = [
            'host_name.require' =>  __('Require',['s'=>__('host_name')]),
            'host_ip.require' =>  __('Require',['s'=>__('host_ip')]),
            'station_code.require' =>  __('Require',['s'=>__('station_code')]),
            'station_code.integer' =>  __('Require',['s'=>__('station_code')]),
        ];
    }
}