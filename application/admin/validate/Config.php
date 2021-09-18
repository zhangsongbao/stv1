<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\admin\validate;
use think\Validate;
class Config extends Validate
{
    protected $rule = [
        'name'  => 'require|unique:config,name^type^is_delete',
        'value'=>'require|unique:config,value^type^is_delete'
    ];
    protected $message  = null;
    protected $scene = [
        'add'  =>  ['name','value'],
        'edit'  => ['name','value'],
    ];
    function __construct()
    {
        $this->message = [
            'name.require' => __('Require',['s'=>__("name")]),
            'name.unique' => __('Unique',['s'=>__("name")]),
            'value.require' => __('Require',['s'=>__("value")]),
            'value.unique' => __('Unique',['s'=>__("value")]),
        ];
    }
}