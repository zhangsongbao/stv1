<?php

namespace app\purchase\model;

use hnzl\HnzlModel;

class Supplier extends HnzlModel
{
    protected $table = "hnzl_supplier";
    public $allowField = true;
    public $softDelete = false;
    protected $pk = 'id';
    //是否站点权限搜索
    public function attachment()
    {
        return $this->hasMany('\app\admin\model\Attachment', 'key', 'attach_key')->field('key,upload_name as name,url,use_area');
    }
    public static function getSupplierName($id)
    {
        return self::where(['id' => $id])->value('supplier_name');
    }
}
