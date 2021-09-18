<?php
namespace app\purchase\model;
use hnzl\HnzlModel;

/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-04-06 09:28:24
 */
class Formula  extends HnzlModel{
    protected $table = "hnzl_formula";
    //默认添加编辑true过滤所有  限定使用数组格式 例如 ['silo_code'];
    public $allowField=true;
    //有添加/编辑字段  权限定义 优先使用添加/编辑 限定使用数组格式 例如 ['silo_code'];
    public $allowFieldAdd=true;
    public $allowFieldEdit=true;
    //软删除
    public $softDelete=false;
    //搜搜字段 默认null 全部
    public $searchField=null;

    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2021-4-6 上午11:28
     * @name: 计算相关
     */
    public function checkFormula(&$data){
        if(!$data['formula']){
            if(preg_match('/[\$][\s\S]*/',$data['formula'])){
                error('');
            };
            //替换指定字符
            $data['formula_php'] =str_replace(['{%cal_weight%}', '{%convert_weight%}', '{%price%}','\n'], [
                '$cal_weight',
                '$convert_weight',
                '$price',
                ';\n'
            ],$data['formula']);
            //执行一次模拟运算
            $cal_weight=10000;
            $convert_weight=10;
            $price=500;
            eval('$num='.  $data['formula_php'].' ');
            if($num){
                return $num;
            }
        }
    }
}
