<?php
/**
 * @author : yhc

 * @date : 2020/10/10 10:41
 * @name :
 */
namespace  app\admin\logic;
class AuthRule {
    public static function checkRole($userId='',$url=''){
        return \hnzl\model\AuthRule::checkRole($userId,$url);
    }
}