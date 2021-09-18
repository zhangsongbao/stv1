<?php
/*
 * @Author: slh
 * @Date: 2021-07-26
 */

namespace app\sales\controller;

use hnzl\HnzlController;

class SalesContract extends HnzlController
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
        $this->model = model('app\sales\model\SalesContract');
        //载入验证器
        $this->validate = new \app\sales\validate\SalesContract();
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

        //我的合同
        if ($with === 'my_contract') {
            $where[] = ['salesman_id', '=', $this->userId];
        }

        $model = $model->with(['attach','review']);

        //客户列表
        if ($with === 'customer') {
            $model = model('\app\partner\model\Customer');

            $model = $model->with(['partner' => function ($query) {
            }]);
        }
         //客户列表
         if ($with === 'review_index') {
            $model = model('\app\sales\model\ReviewSetting');
            
        }
    }
    /**
     * @author : slh
     * @date: 2021-07-26
     * @name: 客户列表
     */

    public function customer_index()
    {
        $this->index('4', 'customer');
    }

    /**
     * @author : slh
     * @date: 2021-07-27
     * @name: 我的合同
     */
    public function my_contract()
    {
        $this->index('4', 'my_contract');
    }

    /**
     * @author : slh
     * @date: 2021-07-27
     * @name: 审核列表
     */
    public function review_index()
    {
        $this->index('4', 'review_index');
    }



    /**
     * @param $deleteData
     * @author : 
     * @date: 2021-07-27
     * @name: 删除前置
     */
    public function _before_del(&$deleteData)
    {
        error(__('contract_del_error'));
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

    /**
     * @param $insert
     * @author : 
     * @date: 2021-07-27
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert)
    {
        $system_no = 'HTS-' . date('Yd') . $insert['id'] . '-' . $insert['station_code'];
        $this->model->save(['system_no' => $system_no], ['id' => $insert['id']]);
    }
}
