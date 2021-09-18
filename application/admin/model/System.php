<?php
namespace app\admin\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-25 14:56:10
 */
class System  extends HnzlModel{
    protected $table = "hnzl_system";
    //默认添加编辑true过滤所有  限定使用数组格式 例如 ['silo_code'];
    public $allowField=true;
    //有添加/编辑字段  权限定义 优先使用添加/编辑 限定使用数组格式 例如 ['silo_code'];
    public $allowFieldAdd=true;
    public $allowFieldEdit=true;
    //软删除
    public $softDelete='is_delete';
    //搜搜字段 默认null 全部
    public $searchField=null;
    public function parent($pid,$field='id')
    {
        return $this->where([
            'id'=>['=',$pid],
            'is_delete'=>['=',0]
        ])->field($field)->find();
    }
}