<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫

 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class MaterialMaster extends Validate
{
    protected $rule = [
        'material_code' => 'integer|require|unique:material_master',
        'material_name' => 'require|unique:material_master',
        'sort' => 'integer',
        'material_cate_id' => 'integer|require',
    ];

    protected $message = null;
    protected $scene = [
        'add' => ['material_code', 'material_name', 'sort', 'material_cate_id',],
        'edit' => ['material_code', 'material_name', 'sort', 'material_cate_id',],
    ];

    function __construct()
    {
        $this->message = [
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'material_code.unique' => __('Unique', ['s' => __('material_code')]),
            'material_name.require' => __('Require', ['s' => __('material_name')]),
            'material_name.unique' => __('Unique', ['s' => __('material_name')]),
            'sort.integer' => __('Integer', ['s' => __('sort')]),
            'material_cate_id.integer' => __('Integer', ['s' => __('material_cate_id')]),
            'material_cate_id.require' => __('Require', ['s' => __('material_cate_id')]),

        ];
    }
}