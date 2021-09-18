<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 15:23:17
 */
namespace app\laboratory\controller;
use hnzl\HnzlController;
class Host extends HnzlController{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['hosts'];
    public function _initialize(){
        parent::_initialize();
         //载入model
        $this->model=model('app\laboratory\model\Host');
        //载入验证器
        $this->validate=new \app\laboratory\validate\Host();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     `
     * @date: 2020-12-18 15:23:17
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->withCount('hostSilo');
    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){
        $code=$this->model->max("host_code");
        if($code){
            $insert['host_code']=config('code.host_code_step')+$code;
        }else{
            $insert['host_code']=config('code.host_code');
        }
    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        redis('hnzl_laboratory_host*',null);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){
        redis('hnzl_laboratory_host*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-18 15:23:17
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data){

    }

    /**
     * @author : yhc
     
     * @date: 2020/12/22 10:10
     * @name: 获取用户站点主机
     */
    public function hosts(){
        $where=[];
        $station=$this->request->post('station_code/d');
        if(isset($station)){
            if(!in_array($station,auth()->stations()))
            {
                $station=-1;
            }
            $where[]=['station_code','=',$station];
        }else{
            if (isset($this->model->station_code)) {
                if(count($this->stationWhere())>1){$where[]=$this->stationWhere();}
            }
        }
        return success(__('Success'),$this->model->where($where)->column('host_code,host_name'));
    }
}
