<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class Recipe extends Validate
{
    protected $rule = [
        'recipe_code' => 'integer',
        'material_code' => 'require',
        'recipe_name' => 'require',
        'station_code' => 'integer|require',
        'host_code' => 'integer|require',

    ];

    protected $message = null;
    protected $scene = [
        'add' => ['recipe_code', 'material_code', 'recipe_name', 'station_code', 'host_code',],
        'edit' => ['material_code', 'recipe_name', 'station_code', 'host_code',],
    ];

    function __construct()
    {
        $this->message = [
            'recipe_code.integer' => __('Integer', ['s' => __('recipe_code')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'recipe_name.require' => __('Require', ['s' => __('recipe_name')]),
            'station_code.integer' => __('Integer', ['s' => __('station_code')]),
            'host_code.integer' => __('Integer', ['s' => __('host_code')]),
            'station_code.require' => __('Require', ['s' => __('station_code')]),
            'host_code.require' => __('Require', ['s' => __('host_code')]),
        ];
    }
}