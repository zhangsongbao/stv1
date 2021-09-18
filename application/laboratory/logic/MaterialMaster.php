<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/11/07 11:16
 * @name: 获取所有配置信息
 */
namespace  app\laboratory\logic;
class MaterialMaster{

    /**
     * @param $name
     * @return array
     * @author : 萤火虫
     
     * @date: 2021-3-5 下午2:32
     * @name: 获取物料编码
     */
    public static function materialCodes($name){
        if(empty($name)){
            return [];
        }
        $res=\app\laboratory\model\MaterialMaster::materialCodes($name);

        return $res?array_column($res->toArray(),'material_code'):[];
    }
}