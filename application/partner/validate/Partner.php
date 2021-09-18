<?php
/**
 * @author : 
 * @date: 2020/07/23
 * @name:
 */
namespace app\partner\validate;
use think\Validate;
class Partner extends Validate
{
    protected $rule =[
        'name' => 'require',
        'id'   => 'require'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['name'],
        'edit'  => ['name','id'],
        'import'  => [],
    ];

    public function sceneCheck(){
        return $this->only(['name'])
        	->append('name', 'unique:partner,name');
    }

    public function sceneEditCheck(){
        return $this->only(['name'])
        	->append('name', 'unique:partner,name^id');
    }


    function __construct()
    {
        $this->message = [
            'name.require' =>  __('Require',['s'=>__('name')]),
            'name.unique' =>  __('Unique',['s'=>__('name')]),
            'id.require' =>  __('Require',['s'=>__('id')]),
        ];
    }
}