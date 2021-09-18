<?php
namespace hnzl;
/**
 * @author : yhc

 * @date : 2020/10/9 7:57
 * @name :
 */
use think\Model;
class HnzlModel extends Model{
    /**
     * @name:定义允许修改的字段  true为全部
     */
    public $allowField=true;
    /**
     * @name:定义是否软删除
     */
    public $softDelete=false;

    //查询条件  空数组允许所有

    //有添加/编辑字段  权限定义 优先使用添加/编辑 限定使用数组格式 例如 ['silo_code'];
    public $allowFieldAdd=true;
    public $allowFieldEdit=true;
    //搜搜字段 默认null 或[] 全部
    public $searchField=null;


    public function getOp(){
        return $this->op;
    }
}