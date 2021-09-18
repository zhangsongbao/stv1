<?php
namespace app\notice\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-08 11:32:02
 */
class Notice  extends HnzlModel{
    protected $table = "hnzl_notice";
    public $allowField=true;
    public $softDelete=false;
}