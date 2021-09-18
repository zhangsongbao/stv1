<?php
/**
 * @author : yhc

 * @date : 2020/10/10 10:41
 * @name :
 */
namespace  app\admin\logic;
class Station {
    protected  $model;

    public static function station($userId=null){
        return \app\admin\model\Station::stations($field='value,name',$userId);
    }
    public static function aa(){

    }
}