<?php
/*
 * @auther 
 * @create_time 2021-07-13 15:12:41
 */
namespace app\partner\controller;
use hnzl\HnzlController;
class Customer extends HnzlController{
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
        protected $import = [
            'limit' => '10000',
            'type' => 'insertAll',
            'unique'=>null,
            //表格内唯一  单字段唯一  多字段不提供校验
            //其他验证 请在验证器内定义验证场景 import
            // import验证规则 禁止定义unique
            //insertAll为sql一次执行 insertAll 一般默认不进行后续数据处理  数据必须严格标准
            //  saveAll为单条添加 执行add方法逻辑 会返回insertId 可以在添加后进行其他数据处理  不建议使用saveAll
        ];
        public function _initialize(){
            parent::_initialize();
            //载入model
            $this->model=model('app\partner\model\Customer');
            //载入验证器
            $this->validate=new \app\partner\validate\Customer();
        }
        /**
         * @param $where
         * @param $model
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 查询前置   用于处理查询条件 添加联查等
         */
        public function _before_index(&$where,&$model,$with=true){
           
          // $model = $model->with('partner');
           if($with === 'partner'){
               $model = model('app\partner\model\Partner');
           }
        }
        /**
         * @param  $result  array  array("total" => $total, "rows" => $list);
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 查询后置   用于处理查询结果
         */
        public function _end_index(&$result){

        }
        //合作伙伴列表
        public function partner_index(){
            $this->index('4','partner');
        }
        /**
         * @param $insert
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 添加前置   用于处理编辑前数据
         */
        public function _before_add(&$insert){
            //加入 添加人id
            $insert['user_id'] = $this->userId;
            //同一个站点，同一个合作伙伴编号，只能有一条记录
        }
        /**
         * @param $insert
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 添加后置  用户处理编辑后连带处理
         */
        public function _end_add(&$insert){

        }
        /**
         * @param $updateArr
         * @param $oldData
         * @author : 
         
         * @date: 2021-07-13 15:12:41
         * @name: 编辑前置   用于处理编辑前数据
         */
        public function _before_edit(&$updateArr, $oldData){
             //同一个站点，同一个合作伙伴编号，只能有一条记录
        }
        /**
         * @param $updateArr
         * @param $oldData
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 编辑后置  用户处理编辑后连带处理
         */
        public function _end_edit(&$updateArr, $oldData){
            
        }

        /**
         * @param $where
         * @param $model
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 查看前置（单条数据查看）
         */
        public function _before_view(&$where,&$model){
            $model = $model->with('partner');
        }
        /**
         * @param $data
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 查看后置（单条数据查看）
         */
        public function _end_view($data){

        }
        /**
         * @param $deleteData
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 删除前置
         */
        public function _before_del(&$deleteData){
                error(__('customer_del_error'));
        }
        /**
         * @param $deleteData
         * @author : 
         * @date: 2021-07-13 15:12:41
         * @name: 删除后置
         */
        public function _end_del(&$deleteData){

        }

        /**
         * @param $deleteData
         * @author :
         * @date: 2020-12-18 15:23:17
         * @name: 删除后置
         */
        public function _before_delAll(&$deleteData){

        }
        /**
         * @param $deleteData
         * @author :
         
         * @date: 2020-12-18 15:23:17
         * @name: 自定义导入校验
         */
        public function _import_data_check(&$data){

        }
        /**
         * @param $data
         * @author : 萤火虫
         * @date: 2020/11/19 11:45
         * @name: 导入前置
         */
        protected function _before_import(&$data){

        }
        /**
         * @param $data
         * @author : 萤火虫
         * @date: 2020/11/24 11:24
         * @name:导入后置
         */
        protected function _end_import(&$data){

        }
    }
