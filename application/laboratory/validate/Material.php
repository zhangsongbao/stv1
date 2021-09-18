<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class Material extends Validate
{
    protected $rule = [
        'material_code' => 'integer|require',
        'material_name' => 'require',
        'material_cate_id' => 'integer',

    ];

    protected $message = null;
    protected $scene = [
        'add' => ['material_code', 'material_name'],
        'edit' => ['material_code', 'material_name'],
    ];

    function __construct()
    {
        $this->message = [
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'material_name.require' => __('Require', ['s' => __('material_name')]),
            'material_cate_id.integer' => __('Integer', ['s' => __('material_cate_id')]),
        ];
    }
}