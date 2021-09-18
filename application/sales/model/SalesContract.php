<?php
namespace app\sales\model;
use hnzl\HnzlModel;


class SalesContract  extends HnzlModel{
        
    //默认添加编辑true过滤所有  限定使用数组格式 例如 ['silo_code'];
    public $allowField=true;
    //有添加/编辑字段  权限定义 优先使用添加/编辑 限定使用数组格式 例如 ['silo_code'];
    public $allowFieldAdd=true;
    public $allowFieldEdit=true;
    //软删除
    public $softDelete=false;
    //搜搜字段 默认null 全部
    public $searchField=null;


    public function attach(){
        return $this->hasMany('\app\admin\model\Attachment','key','attach_key')->field('key,name,url,use_area');
    }
    
    public function customer(){
        return $this->hasOne('\app\partner\model\Customer','id','customer_id');
    }

    public function review(){
        return $this->hasOne('\app\sales\model\ReviewSetting','user_id','review_id');
    }

}