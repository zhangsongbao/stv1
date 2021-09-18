<?php
/**
 * @author : yhc
 
 * @date : 2020/10/14 9:20
 * @name :
 */
namespace app\log\model;
use hnzl\HnzlModel;

class Log extends HnzlModel{
    public static $logType=[
        0=>'登录',
        10=>'登录失败',
        1=>'添加',
        2=>'删除',
        3=>'编辑',
        4=>'查询',
        5=>'导入',
        6=>'导出',
        11=>'批量添加',
        22=>'批量删除',
        33=>'批量编辑',
        44=>'列表查询',
    ];
}