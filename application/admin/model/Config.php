<?php
namespace app\admin\model;
use hnzl\HnzlModel;
/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-07 08:48:04
 */
class Config  extends HnzlModel{
    public $allowField=true;
    public $softDelete='is_delete';
    //配置类型
    protected $configTypes=[
        'default','user_type','user_station','station_title'
    ];
    /**
     * @param string $configName
     * @return array|bool|mixed
     * @author : yhc

     * @date: 2020/11/09 09:54
     * @name: 获取单个配置信息
     */
    public static function config(string $configName,$value='value,name'){
        $cacheKey='admin_config'.$configName.$value;
        if(!$config=redis($cacheKey)){
            $config=self::where([
                ['is_delete','=','0'],
                ['type','=',$configName]
            ])->column($value);
            redis($cacheKey,$config);
        }
        return $config;
    }

}