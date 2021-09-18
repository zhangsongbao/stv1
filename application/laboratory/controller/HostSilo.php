<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-18 17:01:12
 */
namespace app\laboratory\controller;
use app\laboratory\model\SiloMar;
use hnzl\HnzlController;

class HostSilo extends HnzlController{
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
        $this->model=model('app\laboratory\model\HostSilo');
        //载入验证器
        $this->validate=new \app\laboratory\validate\HostSilo();
    }
    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){

    }
    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result){

    }
    /**
     * @param $insert
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert){

            $code=$this->model->where([
                'host_code'=>$insert['host_code'],
                'station_code'=>$insert['station_code']
            ])->max("silo_code");
            if($code){
                $insert['silo_code']=config('code.silo_code_step')+$code;
            }else{
                $insert['silo_code']=$insert['host_code']+config('code.silo_code_step');
            }


    }
    /**
     * @param $insert
     * @author :
     
     * @date: 2020-12-18 17:01:12
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert){
        redis('hnzl_laboratory_hostSilo*',null);
    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData){

    }
    /**
     * @param $updateArr
     * @param $oldData
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData){

        if(isset($updateArr['material_code'])){
            if($updateArr['material_code']!=$oldData['material_code']){
                $where=[
                    'station_code'=>$updateArr['station_code'],
                    'host_code'=>$updateArr['host_code']
                ];
                $silo_info = $this->model->where($where)->order('silo_code  desc')->select();
                if (!$silo_info) {
                    error(__('No modification occurred'));
                }

                $data=[
                    'create_time'=>time(),
                    'silo_mar'=>'',
                    'silo_mar_str'=>'',
                    'add_user'=>$this->userId,
                    'station_code'=>$oldData['station_code'],
                    'host_code'=>$oldData['host_code'],
                    'update_time'=>time()
                ];
                $data=array_merge($data,\app\laboratory\model\HostSilo::getSiloMarStr($silo_info->toArray(),1));
                //查询主机-料位信息表里是否有此修改记录 有则不添
                $HostSiloMar=new SiloMar();
                $info = $HostSiloMar->where('silo_mar',$data['silo_mar'])->find();

                if (!$info){
                    $HostSiloMar->allowField(true)->save($data);
                }
                (model('app\laboratory\model\SiloMarHistory'))->allowField(true)->save($data);
            }
        }
        redis('hnzl_laboratory_hostSilo*',null);
    }

    /**
     * @param $where
     * @param $model
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where,&$model){

    }
    /**
     * @param $data
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 删除前置
     */
    public function _before_del(&$deleteData){

    }
    /**
     * @param $deleteData
     * @author : 
     
     * @date: 2020-12-18 17:01:12
     * @name: 删除后置
     */
    public function _end_del(&$deleteData){

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
     * @param $deleteData
     * @author :
     
     * @date: 2020-12-18 15:23:17
     * @name: 删除后置
     */
     public function _before_delAll(&$deleteData){

     }

    /**
     * @author : yhc
     
     * @date: 2020/12/22 10:10
     * @name: 获取主机料位信息
     */
    public function hostSilos(){
        $host=$this->request->post('host_code/d');
        $station_code=$this->request->post('station_code/d');
        if(is_null($host)|| !$station_code){
            error(__('Params Error'));
        }
        if(!in_array($station_code,auth()->stations()))
        {
            error(__('Params Error'));
        }
        $where[]=['host_code','=',$host];
        $where[]=['station_code','=',$station_code];

        return success(__('Success'),$this->model->where($where)->field('silo_code,silo_name,material_code,material_name')->select());
    }

    /**
     * @author : yhc
     
     * @date: 2020/12/22 11:45
     * @name: 编辑料位-材料对照信息
     */
    public function editMaterial(){
        $getData=$this->request->post('getData');
        if($getData){
            $where=[
                ['host_code','=',$this->request->post('host_code','')],
                ['station_code','=',$this->request->post('station_code','')]
             ];
            $host=new \app\laboratory\model\Host();
            return success(__('Success'),$host->where($where)->with('hostSilo')->find());
        }
        $this->model->allowFieldEdit=['material_code','material_name'];
        $this->validate=new \app\laboratory\validate\HostSiloEdit();
        $this->edit();
    }

}
