<?php
/*
 * @Author: your name
 * @Date: 2021-07-27 11:20:56
 * @LastEditTime: 2021-07-27 11:21:03
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: \stv1\application\sales\model\ProductionBill.php
 */

namespace app\sales\model;

use hnzl\HnzlModel;


class ProductionBill  extends HnzlModel
{

    //默认添加编辑true过滤所有  限定使用数组格式 例如 ['silo_code'];
    public $allowField = true;
    //有添加/编辑字段  权限定义 优先使用添加/编辑 限定使用数组格式 例如 ['silo_code'];
    public $allowFieldAdd = true;
    public $allowFieldEdit = true;
    //软删除
    public $softDelete = false;
    //搜搜字段 默认null 全部
    public $searchField = null;
}
