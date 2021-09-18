<?php
/*
 * @Author: slh
 * @Date: 2021-07-26
 */
namespace app\sales\controller;
use hnzl\HnzlController;
class SupplyBill extends HnzlController{
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
      
        public function _initialize(){
            parent::_initialize();
            //载入model
            $this->model=model('app\sales\model\SupplyBill');
            //载入验证器
            $this->validate=new \app\sales\validate\SupplyBill();
        }


        /**
         * @param $where
         * @param $model
         * @author : 
         * @date: 2021-07-26
         * @name: 查询前置   用于处理查询条件 添加联查等
         */
        public function _before_index(&$where,&$model,$with=true){

            
            $model = $model->with('material');
            //客户列表
            if($with === 'contract_index'){
                $model = model('app\sales\model\SalesContract');
                $model = $model->with(['customer'=>['partner']]);
            }


            
        }
         /**
         * @param $insert
         * @author : 
         * @date: 2021-07-26
         * @name: 添加前置   用于处理编辑前数据
         */
        public function contract_index(){
           $this->index('4','contract_index');
        }
         /**
         * @param $insert
         * @author : 
         * @date: 2021-07-26
         * @name: 添加前置   用于处理编辑前数据
         */
        public function _before_add(&$insert){
            //供货员 添加人
            $insert['supplyman_id'] = $this->userId;
            $insert['create_uid'] = $this->userId;
        }

        /**
         * @param $insert
         * @author : 
         * @date: 2021-07-27
         * @name: 添加后置  用户处理编辑后连带处理
         */
        public function _end_add(&$insert){
            $system_no = 'GHD-' . date('Yds') . $insert['id'] . '-' . $insert['station_code'];
            $this->model->save(['supplybill_no'=>$system_no],['id'=>$insert['id']]);
        }

        
      
        /**
         * @param $deleteData
         * @author : 
         * @date: 2021-07-26
         * @name: 删除前置
         */
        public function _before_del(&$deleteData){
                error(__('contract_del_error'));
        }

    }


