<?php

namespace app\admin\model;

use hnzl\HnzlModel;
use think\Db;

/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-13 09:04:07
 */

class UserFile extends HnzlModel
{
    public $allowField = true;
    public $softDelete = false;
    protected $pk = 'user_id';

    public function user()
    {
        return $this->hasOne('hnzl\model\User', 'id', 'user_id')->field('id,user_name,real_name,mobile');
    }

    /**
     * @param int $user_id
     * @return array|bool|mixed
     * @author : yhc
     * @date: 2020/11/09 09:54
     * @name: 获取单个配置信息
     */
    public static function config(int $user_id, $field = 'used,file_count,type,max_use,max_size')
    {
        $config = self::where([
            ['user_id', '=', $user_id],
        ])->field($field)->find();
        return $config;
    }

    /**
     * @param int $user_id
     * @param string $size
     * @param string $used
     * @author : yhc
     * @date: 2020/11/13 11:50
     * @name: 改变用户上传记录
     */
    public static function change(int $user_id, $size)
    {
        return self::where('user_id', $user_id)
            ->inc('file_count', 1)
            ->update(['used' => self::size($size, 1)]);
    }
    public static function  des($user_id,$size){
        $userSize=self::where('user_id',$user_id)->value('used');
        $used=self::size($userSize);
        return self::where('user_id', $user_id)
            ->inc('file_count', -1)
            ->update(['used' => self::size($used-$size, 1)]);
    }

    /**
     * @param $size
     * @param int $type  大于0 数字转bkmg  0 bkmg转数字
     * @return float|int|string
     * @author : yhc
     
     * @date: 2020/11/23 09:29
     * @name: 文件大小转换
     */
    public static function size($size, $type = 0)
    {
        if ($type == 0) {
            $typeDict = ['b' => 0, 'k' => 1, 'kb' => 1, 'm' => 2, 'mb' => 2, 'gb' => 3, 'g' => 3];
            preg_match('/([0-9]*\.?[0-9]*)([a-zA-Z]{1,2})/', strtolower($size), $matches);
            if (!isset($matches[2])) {
                $matches[2]='b';
            }
            $type = strtolower($matches[2]);
            $useSize = (float)$size * pow(1024, isset($typeDict[$type]) ? $typeDict[$type] : 0);
            return $useSize;
        } else {
            $types = [1 => 'b', 2 => 'k', 3 => 'm', 4 => 'g'];
            if ($size > 1024) {
                $size = round($size / 1024, 2);
                return self::size($size, $type + 1);
            } else {
                return $size . ($types[$type] ? $types[$type] : '');
            }
        }
    }

}