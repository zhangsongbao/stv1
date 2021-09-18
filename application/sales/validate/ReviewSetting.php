<?php
/*
 * @Author: slh
 * @Date: 2021-07-26 16:26:55
 */

namespace app\sales\validate;
use think\Validate;
class ReviewSetting extends Validate
{
    protected $rule =[
        'user_id' => 'require',
        'station_code'   => 'require'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['user_id','station_code'],
        'edit'  => ['user_id','station_code'],
    ];

    function __construct()
    {
        $this->message = [
            'user_id.require' =>  __('Require',['s'=>__('user_id')]),
            'station_code.require' =>  __('Require',['s'=>__('station_code')]),
        ];
    }
}