<?php
/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-07 08:48:04
 */
namespace app\admin\controller;

class UserType extends Config {
    public function _initialize(){
        parent::_initialize();
        $this->type='user_type';
    }

}
