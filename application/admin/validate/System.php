<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\admin\validate;

use think\Validate;

class System extends Validate
{
    protected $rule = [
        'name' => 'require|length:0,50',
        'pid' => 'require|integer',
        'note' => 'length:0,500',
        'sort' => 'require|integer',
        'code' => 'require|length:0,50',

    ];

    protected $message = null;
    protected $scene = [
        'add' => ['name', 'pid', 'note', 'sort', 'code',],
        'edit' => ['name', 'pid', 'note', 'sort', 'code',],
    ];

    function __construct()
    {
        $this->message = [
            'name.require' => __('Require', ['s' => __('name')]),
            'name.length' => __('Length', ['s' => __('name')]),
            'pid.require' => __('Require', ['s' => __('pid')]),
            'pid.integer' => __('Integer', ['s' => __('pid')]),

            'note.length' => __('Length', ['s' => __('note')]),
            'sort.require' => __('Require', ['s' => __('sort')]),
            'sort.integer' => __('Integer', ['s' => __('sort')]),
            'code.require' => __('Require', ['s' => __('code')]),
            'code.length' => __('Length', ['s' => __('code')]),

        ];
    }
}