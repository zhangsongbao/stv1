<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/10/28 10:56
 * @name:
 */
namespace app\admin\model;
use hnzl\HnzlModel;
use think\Db;

class UserType extends HnzlModel{
    public $allowField=true;
    public $softDelete=false;
}