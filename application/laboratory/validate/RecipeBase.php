<?php
/**
 * Created by PhpStorm.
 * @author : 萤火虫
 
 * @date: 2020/11/06 15:33
 * @name:
 */
namespace app\laboratory\validate;
use think\Validate;
class RecipeBase extends Validate
{
    protected $rule =[
        'material_code'  => 'require|integer',
        'material_num'  => 'require',
        'recipe_code'  => 'integer',
        'float_up'  => 'require|float',
        'float_down'  => 'require|float',
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['material_code','material_num','recipe_code','float_up','float_down',],
        'edit'  => ['material_code','material_num','recipe_code','float_up','float_down',],
    ];
    function __construct()
    {
        $this->message = [
            'material_code.require' =>  __('Require',['s'=>__('material_code')]),
            'material_num.require' =>  __('Require',['s'=>__('material_num')]),
            'recipe_code.integer' =>  __('Integer',['s'=>__('recipe_code')]),
            'material_code.integer' =>  __('Int',['s'=>__('material_code')]),
            'float_up.require' =>  __('Require',['s'=>__('float_up')]),
            'float_up.float' =>  __('Float',['s'=>__('float_up')]),
            'float_down.require' =>  __('Require',['s'=>__('float_down')]),
            'float_down.float' =>  __('Float',['s'=>__('float_down')]),
        ];
    }
}