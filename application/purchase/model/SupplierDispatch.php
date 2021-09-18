<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-08 11:20:41
 */
class SupplierDispatch  extends HnzlModel{
    protected $table = "hnzl_supplier_dispatch";
    public $allowField=true;
    public $softDelete=false;
    //是否站点权限搜索
    public $station_code='purchase_station_code';
    public $allowFieldAdd=true;
    public $allowFieldEdit=['contract_id', 'material_code', 'material_name', 'supplier_name', 'purchase_station_code',
        'receive_station_code', 'unit', 'bill', 'supplier_time', 'supplier_weight', 'car_plate', 'note', 'attach_id',];
    public function attachment()
    {
        return $this->hasOne('\app\admin\model\Attachment', 'id', 'attach_id')->field('id,key,upload_name as name,url,use_area');
    }
}