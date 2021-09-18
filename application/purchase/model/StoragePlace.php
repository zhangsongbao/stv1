<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

class StoragePlace extends HnzlModel{
    protected $table = "hnzl_storage_place";
    public $allowField=true;
    public $softDelete=false;
    protected $pk='id';
    //是否站点权限搜索
    public $station_code='station_code';
    public function station()
    {
        return $this->hasOne('\app\admin\model\Station','value','station_code')->bind('title');
    }
}