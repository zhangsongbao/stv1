<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\purchase\validate;
use think\Validate;
class Formula extends Validate
{
    protected $rule =[
        'name'  => 'require|length:0,50',
'formula'  => 'require|length:0,500',

    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['name','formula',],
        'edit'  => ['name','formula',],
    ];
    function __construct()
    {
        $this->message = [
            'name.require' =>  __('Require',['s'=>__('name')]),
'name.length' =>  __('Length',['s'=>__('name')]),
'formula.require' =>  __('Require',['s'=>__('formula')]),
'formula.length' =>  __('Length',['s'=>__('formula')]),

        ];
    }
}