<?php

namespace app\purchase\validate;
use think\Validate;

class Supplier extends Validate{
    protected $rule =[
        'supplier_name'  => 'require|length:0,200',
        'certificate_num'=>'require|length:0,100',
        'business_scope'=>'require|length:0,300',
        'address'=>'require|length:0,300',
        'contacts_user'=>'require|length:0,200',
        'contacts_tel'=>'require|length:0,300',
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['supplier_name','certificate_num','business_scope','address','contacts_user','contacts_tel'],
        'edit'   => ['supplier_name','certificate_num','business_scope','address','contacts_user','contacts_tel'],
    ];
    function __construct()
    {
        $this->message = [
            'supplier_name.require' =>  __('Require',['s'=>__('supplier_name')]),
            'certificate_num.require' =>  __('Require',['s'=>__('certificate_num')]),
            'business_scope.require' =>  __('Require',['s'=>__('business_scope')]),
            'address.require' =>  __('Require',['s'=>__('address')]),
            'contacts_user.require' =>  __('Require',['s'=>__('contacts_user')]),
            'contacts_tel.require' =>  __('Require',['s'=>__('contacts_tel')]),

            'supplier_name.length' =>  __('Length',['s'=>__('supplier_name')]),
            'certificate_num.length' =>  __('Length',['s'=>__('certificate_num')]),
            'business_scope.length' =>  __('Length',['s'=>__('business_scope')]),
            'address.length' =>  __('Length',['s'=>__('address')]),
            'contacts_user.length' =>  __('Length',['s'=>__('contacts_user')]),
            'contacts_tel.length' =>  __('Length',['s'=>__('contacts_tel')]),
          
        ];
    }
}