<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */

namespace app\laboratory\validate;

use think\Validate;

class MaterialCate extends Validate
{
    protected $rule = [
        'material_cate_id'=>'require|integer',
        'cate_name' => 'require|unique:material_cate',
        'status' => 'integer|require',
    ];

    protected $message = null;
    protected $scene = [
        'add' => ['material_cate_id', 'cate_name', 'status'],
        'edit' => ['material_cate_id','cate_name', 'status'],
    ];

    function __construct()
    {
        $this->message = [

            'cate_name.require' => __('Require', ['s' => __('cate_name')]),
            'cate_name.unique' => __('Unique', ['s' => __('cate_name')]),
            'material_cate_id.integer' => __('Int', ['s' => __('material_cate_id')]),
            'material_cate_id.require' => __('Require', ['s' => __('material_cate_id')]),
            'status.integer' => __('Integer', ['s' => __('status')]),
            'status.require' => __('Require', ['s' => __('status')]),

        ];
    }
}