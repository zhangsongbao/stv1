<?php

namespace app\purchase\validate;

use think\Validate;

class StoragePlace extends Validate
{
    protected $rule = [
        'name'  => 'require|length:0,50',
        'station_code' => 'integer|require'

    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['name', 'station_code'],
        'edit'   => ['name', 'station_code'],
    ];
    function __construct()
    {
        $this->message = [
            'name.require' =>  __('Require', ['s' => __('name')]),
            'name.length' =>  __('Length', ['s' => __('name')]),
            'station_code.require' =>  __('Require', ['s' => __('station_code')]),
            'station_code.integer' =>  __('Integer', ['s' => __('station_code')])
        ];
    }
}
