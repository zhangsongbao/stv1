<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class HostSiloEdit extends Validate
{
    protected $rule = [


    ];

    protected $message = null;
    protected $scene = [
        'edit' => [''],
    ];

    function __construct()
    {
        $this->message = [

        ];
    }
}