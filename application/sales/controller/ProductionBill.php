<?php
/*
 * @Author: slh
 * @Date: 2021-07-26
 */

namespace app\sales\controller;

use hnzl\HnzlController;

class ProductionBill extends HnzlController
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model = model('app\sales\model\ProductionBill');
        //载入验证器
        $this->validate = new \app\sales\validate\ProductionBill();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     * @date: 2021-07-26
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where, &$model, $with = true)
    {
         //客户列表
         if($with === 'supplybill_index'){
            $model = model('app\sales\model\SupplyBill');
        }
    }
     /**
         * @param $insert
         * @author : 
         * @date: 2021-07-29
         * @name: 添加前置   用于处理编辑前数据
         */
        public function supplybill_index(){
            $this->index('4','supplybill_index');
         }
    /**
     * @param $insert
     * @author : 
     * @date: 2021-07-26
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert)
    {
        //加入 添加人id
        $insert['create_uid'] = $this->userId;
    }
}
