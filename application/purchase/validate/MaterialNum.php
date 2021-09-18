<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\purchase\validate;
use think\Validate;
class MaterialNum extends Validate
{
    protected $rule =[
        'station_code' => 'integer|require',
        'material_code' => 'integer|require|unique:check_history,material_code^station_code',
        'change_num' => 'require',
    ];
    protected $message = null;
    protected $scene = [
        'add' => ['station_code', 'material_code', 'now_num',],
        'edit' => ['station_code', 'material_code',  'now_num',],
    ];

    function __construct()
    {
        $this->message = [
            'station_code.integer' => __('Integer', ['s' => __('station_code')]),
            'material_code.integer' => __('Integer', ['s' => __('material_code')]),
            'material_code.require' => __('Require', ['s' => __('material_code')]),
            'material_code.unique' => __('Unique', ['s' => __('material_code')]),
            'station_code.require' => __('Require', ['s' => __('station_code')]),
            'now_num.require' => __('Require', ['s' => __('now_num')]),
        ];
    }
}