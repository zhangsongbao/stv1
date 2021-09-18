<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/11 10:51
 * @name:
 */
return [
    'user_id' => [
        'salt' =>'hnzl_zztd',//可以随便输入你自己加密盐
        'length' => '8',//加密后字符串长度
    ],
    'downloadParams'=>[
        'salt'=>'hnzl_downloads',
        'length'=>'255'
    ],
    'file_name' => [
        'salt' =>'hnzl_file',//可以随便输入你自己加密盐
        'length' => '16',//加密后字符串长度
    ],

];