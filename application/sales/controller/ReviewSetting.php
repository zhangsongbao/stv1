<?php
/*
 * @Author: slh
 * @Date: 2021-07-26
 */

namespace app\sales\controller;

use hnzl\HnzlController;

class ReviewSetting extends HnzlController
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
        $this->model = model('app\sales\model\ReviewSetting');
        //载入验证器
        $this->validate = new \app\sales\validate\ReviewSetting();
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
    }
}
