<?php
/**
 * @author : yhc

 * @date: 2020/10/14 下午2:35
 * @name:
 */
namespace app\admin\validate;
use think\Validate;
class AuthGroup extends Validate
{
    protected $rule = [
        'name'  => 'require|unique:auth_group',
        'rules' =>'require'
    ];
    protected $message  = null;
    protected $scene = [
        'add'  =>  ['name','rules'],
        'edit'  =>  ['title','rules'],
    ];
    function __construct()
    {
        $this->message = [
            'name.require' => __('Require',['s'=>__("name")]),
            'name.unique' => __('Unique',['s'=>__("name")]),
            'title.rules' => __('Require',['s'=>__("rules")]),
        ];
    }
}