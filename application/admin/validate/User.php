<?php
/**
 * @author : yhc

 * @date: 2020/10/14 下午2:35
 * @name:
 */
namespace app\admin\validate;
use think\Validate;
class User extends Validate
{
    protected $rule = [
        'user_name'  => 'require|unique:user,user_name^is_delete',
        'real_name'=>'require',
        'user_group'=>'require',
        'mobile'=>'',
        'weigh'=>'integer',
        'status'=>'integer',
    ];
    protected $message  = null;
    protected $scene = [
        'add'  =>  ['user_name','real_name','weigh'],
        'edit'  => ['user_name','weigh'],

    ];
    function __construct()
    {
        $this->message = [
            'user_name.require' => __('Require',['s'=>__("user_name")]),
            'user_name.unique' => __('Unique',['s'=>__("user_name")]),
            'weigh.integer' => __('Int',['s'=>__("weigh")]),
            'status.integer' => __('Err',['s'=>__("status")]),
            'real_name.require' => __('Require',['s'=>__("real_name")]),
            'user_group.require' => __('Require',['s'=>__("user_group")]),

        ];
    }

}