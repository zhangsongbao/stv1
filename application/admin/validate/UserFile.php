<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\admin\validate;
use think\Validate;
class UserFile extends Validate
{
    protected $rule =[

    ];

    protected $message  = null;
    protected $scene = [
        'add'   => [],
        'edit'  => [],
    ];
    function __construct()
    {
        $this->message = [
        ];
    }
}