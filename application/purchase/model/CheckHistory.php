<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-04 15:50:24
 */
class CheckHistory  extends HnzlModel{
    protected $table = "hnzl_check_history";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    public $station_code='station_code';
    //public $allowFieldAdd=['silo_code','silo_name','host_code','station_code','field_silo','field_recipe','field_cost'];
    //public $allowFieldEdit=true;
    public function user()
    {
        return $this->hasOne('hnzl\model\User', 'id', 'update_user')->bind(['update_user_name'=>'real_name']);
    }

}