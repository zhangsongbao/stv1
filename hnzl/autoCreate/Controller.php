<?php
/*
 * @auther !$Auther!
 * @create_time !$time!
 */
namespace app\!$module!\controller;
use hnzl\HnzlController;
class !$name! extends HnzlController{
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
            $this->model=model('app\!$module!\model\!$name!');
            //载入验证器
            $this->validate=new \app\!$module!\validate\!$name!();
        }
        /**
         * @param $where
         * @param $model
         * @author : !$Auther!
         * @date: !$time!
         * @name: 查询前置   用于处理查询条件 添加联查等
         */
        public function _before_index(&$where,&$model,$with=true){

        }
        /**
         * @param  $result  array  array("total" => $total, "rows" => $list);
         * @author : !$Auther!
         * @date: !$time!
         * @name: 查询后置   用于处理查询结果
         */
        public function _end_index(&$result){

        }
        /**
         * @param $insert
         * @author : !$Auther!
         * @date: !$time!
         * @name: 添加前置   用于处理编辑前数据
         */
        public function _before_add(&$insert){

        }
        /**
         * @param $insert
         * @author : !$Auther!
         * @date: !$time!
         * @name: 添加后置  用户处理编辑后连带处理
         */
        public function _end_add(&$insert){

        }
        /**
         * @param $updateArr
         * @param $oldData
         * @author : !$Auther!
         
         * @date: !$time!
         * @name: 编辑前置   用于处理编辑前数据
         */
        public function _before_edit(&$updateArr, $oldData){

        }
        /**
         * @param $updateArr
         * @param $oldData
         * @author : !$Auther!
         * @date: !$time!
         * @name: 编辑后置  用户处理编辑后连带处理
         */
        public function _end_edit(&$updateArr, $oldData){

        }

        /**
         * @param $where
         * @param $model
         * @author : !$Auther!
         * @date: !$time!
         * @name: 查看前置（单条数据查看）
         */
        public function _before_view(&$where,&$model){

        }
        /**
         * @param $data
         * @author : !$Auther!
         * @date: !$time!
         * @name: 查看后置（单条数据查看）
         */
        public function _end_view($data){

        }
        /**
         * @param $deleteData
         * @author : !$Auther!
         * @date: !$time!
         * @name: 删除前置
         */
        public function _before_del(&$deleteData){

        }
        /**
         * @param $deleteData
         * @author : !$Auther!
         * @date: !$time!
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
         * @author : yhc
         * @date: 2020/11/19 11:45
         * @name: 导入前置
         */
        protected function _before_import(&$data){

        }
        /**
         * @param $data
         * @author : yhc
         * @date: 2020/11/24 11:24
         * @name:导入后置
         */
        protected function _end_import(&$data){

        }
    }
