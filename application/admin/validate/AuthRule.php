<?php
/**
 * @author : yhc

 * @date: 2020/10/14 下午2:35
 * @name:
 */
namespace app\admin\validate;
use think\Validate;
class AuthRule extends Validate
{
    protected $rule = [
        'name'  => 'require|unique:auth_rule',
        'title' =>'require|unique:auth_rule',
        'weigh'=>'integer',
        'status'=>'integer',
        'pid'=>'integer'
    ];
    protected $message  = null;
    protected $scene = [
        'add'  =>  ['name','title','weigh','status','pid'],
        'edit'  =>['name','title','weigh','status','pid'],
    ];
    function __construct()
    {
        $this->message = [
            'name.require' => __('Require',['s'=>__("name")]),
            'name.unique' => __('Unique',['s'=>__("name")]),
            'title.require' => __('Require',['s'=>__("title")]),
            'title.unique' => __('Unique',['s'=>__("title")]),
            'weigh.integer' => __('Int',['s'=>__("weigh")]),
            'status.integer' => __('Err',['s'=>__("status")]),
            'pid.integer' => __('Err',['s'=>__("pid")]),
        ];
    }
}