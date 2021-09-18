<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/07 11:16
 * @name: 获取所有配置信息
 */
namespace  app\admin\logic;
use app\admin\model\Config as ConfigMd;
class Config{
    /**
     * @param $configName
     * @author : yhc

     * @date: 2020/11/09 10:23
     * @name: 获取key=>value配置接口 $configName 可多传数组、或，隔开string
     */
    public static function getConfig($configName){
        if(!is_array($configName)){
            $configName=explode(',',$configName);
        }
        foreach ($configName as $k=>$v){
            $configs[$v]=ConfigMd::config($v);
        }
        return $configs;
    }
}