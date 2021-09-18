<?php
/*
 * @Author: slh
 * @Date: 2021-07-26 16:26:55
 */

namespace app\sales\validate;

use think\Validate;

class ProductionBill extends Validate
{
    protected $rule = [
        'production_station' => 'require',
        'production_num'   => 'require',
        'supplybill_id' => 'require'
    ];

    protected $message  = null;
    protected $scene = [
        'add'   => ['station_code', 'production_num', 'supplybill_id'],
        'edit'  => ['production_num', 'station_code', 'supplybill_id'],
    ];

    function __construct()
    {
        $this->message = [
            'production_num.require' =>  __('Require', ['s' => __('production_num')]),
            'production_station.require' =>  __('Require', ['s' => __('production_station')]),
            'supplybill_id.require' =>  __('Require', ['s' => __('supplybill_id')])
        ];
    }
}
