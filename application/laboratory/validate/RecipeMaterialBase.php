<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class RecipeMaterialBase extends Validate
{
    protected $rule = [

        'type' => 'integer',
        'silo_mar_id' => 'integer',
        'product_code' => 'integer',
        'host_code' => 'integer',
        'station_code' => 'integer',
        'recipe_code' => 'integer',

    ];

    protected $message = null;
    protected $scene = [
        'add' => ['rmb_id', 'type', 'silo_mar_id', 'product_code', 'host_code', 'station_code', 'recipe_code',],
        'edit' => ['rmb_id', 'type', 'silo_mar_id', 'product_code', 'host_code', 'station_code', 'recipe_code',],
    ];

    function __construct()
    {
        $this->message = [

            'type.integer' => __('Integer', ['s' => __('type')]),
            'silo_mar_id.integer' => __('Integer', ['s' => __('silo_mar_id')]),
            'product_code.integer' => __('Integer', ['s' => __('product_code')]),
            'host_code.integer' => __('Integer', ['s' => __('host_code')]),
            'station_code.integer' => __('Integer', ['s' => __('station_code')]),
            'recipe_code.integer' => __('Integer', ['s' => __('recipe_code')]),

        ];
    }
}