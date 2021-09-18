<?php
/*
 * @Author: slh
 * @Date: 2021-07-26 

 */

namespace app\sales\validate;
use think\Validate;
class salesContract extends Validate
{
    protected $rule =[
        'contract_no' => 'require',
        'project_name'   => 'require',
        'contract_name'=>'require',
        'id'=>'require'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['contract_no','project_name','contract_name'],
        'edit'  => ['contract_no','project_name','contract_name','id'],
        'import'  => [],
    ];

    function __construct()
    {
        $this->message = [
            'contract_no.require' =>  __('Require',['s'=>__('contract_no')]),
            'project_name.Require' =>  __('Require',['s'=>__('project_name')]),
            'contract_name.require' =>  __('Require',['s'=>__('contract_name')]),
            'id.require' =>  __('Require',['s'=>__('id')])
        ];
    }
}