<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class HostSilo extends Validate
{
    protected $rule = [
     
        'host_code' => 'integer|require',
        'silo_name'=>'require|unique:host_silo,silo_name^station_code^host_code',
        'station_code' => 'integer|require',
    ];

    protected $message = null;
    protected $scene = [
        'add' => [ 'host_code', 'station_code','silo_name'],
        'edit' => ['silo_name'],
    ];

    function __construct()
    {
        $this->message = [
     
            'host_code.integer' => __('Integer', ['s' => __('host_code')]),
            'host_code.require' => __('Require', ['s' => __('host_code')]),
                'station_code.require' => __('Require', ['s' => __('station_code')]),
            'silo_name.require' => __('Require', ['s' => __('silo_name')]),
            'silo_name.unique' => __('Unique', ['s' => __('silo_name')]),
            'station_code.integer' => __('Integer', ['s' => __('station_code')]),
        ];
    }
}