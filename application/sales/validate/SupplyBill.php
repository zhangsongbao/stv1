<?php
/*
 * @Author: slh
 * @Date: 2021-07-26 

 */

namespace app\sales\validate;
use think\Validate;
class SupplyBill extends Validate
{
    protected $rule =[
        'contract_id' => 'require',
        'id'=>'require'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['contract_id'],
        'edit'  => ['contract_id','id'],
        'import'  => [],
    ];

    function __construct()
    {
        $this->message = [
            'contract_id.require' =>  __('Require',['s'=>__('contract_id')]),
            'id.require' =>  __('Require',['s'=>__('id')])
        ];
    }
}