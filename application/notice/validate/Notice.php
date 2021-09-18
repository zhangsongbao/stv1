<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\notice\validate;

use think\Validate;
class Notice extends Validate
{
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'to_user' => 'require',
    ];
    protected $message = null;
    protected $scene = [
        'add' => ['title', 'content', 'to_user',],
        'edit' => ['title', 'content',  'to_user',],
    ];
    function __construct()
    {
        $this->message = [
            'title.require' => __('Require', ['s' => __('title')]),
            'content.require' => __('Require', ['s' => __('content')]),
            'to_user.require' => __('Require', ['s' => __('to_user')]),
        ];
    }
}