<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class RmbDetail extends Validate
{
    protected $rule = [
        'silo_code' => 'integer|require',
        'material_code' => 'integer|require',
        'recipe' => 'require|float',
    ];

    protected $message = null;
    protected $scene = [
        'add' => [ 'silo_code', 'material_code', 'recipe'],
        'edit' => [ 'silo_code', 'material_code', 'recipe']
    ];

    function __construct()
    {
        $this->message = [
            'rmb_id.integer' => __('Integer', ['s' => __('rmb_id')]),
            'silo_code.integer' => __('Integer', ['s' => __('silo_code')]),
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),
            'rmb_id.require' => __('Require', ['s' => __('rmb_id')]),
            'silo_code.require' => __('Require', ['s' => __('silo_code')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'recipe.require' => __('Require', ['s' => __('recipe')]),
            'recipe.float' => __('Float', ['s' => __('recipe')]),
        ];
    }
}