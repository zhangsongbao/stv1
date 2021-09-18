<?php
namespace app\admin\model;
use hnzl\HnzlModel;

/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-06 16:52:38
 */
class UserStation  extends HnzlModel{
    public $allowField=true;
    public $softDelete=false;
    public function user(){
        return $this->hasOne('hnzl\model\User','id','user_id')->where('is_delete','0')->bind('user_name,real_name');
    }
}