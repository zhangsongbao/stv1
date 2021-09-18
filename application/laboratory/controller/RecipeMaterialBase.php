<?php
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2020-12-24 09:24:43
 */

namespace app\laboratory\controller;

use app\laboratory\model\RmbDetail;
use app\laboratory\model\SiloMar;
use hnzl\HnzlController;

//生产配比控制器
class RecipeMaterialBase extends HnzlController
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
        $this->model = model('app\laboratory\model\RecipeMaterialBase');
        //载入验证器
        $this->validate = new \app\laboratory\validate\RecipeMaterialBase();
    }

    /**
     * @param $where
     * @param $model
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where, &$model, $with = true)
    {
        //查询当前料位 下配方信息
        $hostCode = $this->request->post('filter.host_code');
        $stationCode = $this->request->post('filter.station_code');
        if (!$hostCode || !$stationCode) {
            error(__('Params Error'));
        }
        $where[] = ['silo_mar_id', '=', \app\laboratory\model\HostSilo::getSiloMarId($hostCode, $stationCode)];
        $model = $model->with('rmbDetail');
    }

    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result)
    {
        $rows = $result['rows'] ? $result['rows']->toArray() : [];
        //处理　材料分配数据
        array_walk($rows, function (&$value, $key) {
            foreach ($value['rmb_detail'] as $k => $v) {
                $value['material_code_' . $v['material_code']] = $v['recipe'];
            }
            unset($value['rmb_detail']);
        });
        $result['rows'] = $rows;
        $hostSilo=new \app\laboratory\model\hostSilo();
        $result['hostSilo']=$hostSilo->getSilo($this->request->post('filter.station_code'),$this->request->post('filter.host_code'));
    }

    /**
     * @param $insert
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert)
    {
        $siloMar = new SiloMar();
        $insert['status'] = 0;
        $str = \app\laboratory\model\HostSilo::getSiloMarStr($insert['rmb_detail']);
        $where = [
            'station_code' => $insert['station_code'],
            'host_code' => $insert['host_code'],
            'silo_mar' => $str['silo_mar']
        ];
        $insert['silo_mar_id'] = $siloMar->where($where)->value('silo_mar_id');
        if (!$insert['silo_mar_id']) {
            error(__('siloMar error'),[],'2');
        }
        $insert['update_user'] = $this->userId;
        $validate = new \app\laboratory\validate\RmbDetail();
        foreach ($insert['rmb_detail'] as $k => $v) {
            if (!$validate->check($v)) {
                error($validate->getError(), '', 2);
            }
        }
        //自动检测是否符合最新的配方
        $insert['status']=$this->checkRecipe($insert);
    }

    public function _end_add(&$insert)
    {
        $this->LoadLang('RmbDetail');
        $RmbDetail = new RmbDetail();
        $id = $insert['id'];
        array_walk($insert['rmb_detail'], function (&$value, $key, $id) {
            $value['rmb_id'] = $id;
        }, $id);
        $allField = $RmbDetail->allowFieldAdd ?? $RmbDetail->allowField;
        $RmbDetail->allowField($allField)->saveAll($insert['rmb_detail']);
    }

    /**
     * @param $updateArr
     * @param $oldData
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData)
    {
        if($oldData['status']==1){
            error(__('not allow edit'),[],'2');
        }
        $siloMar = new SiloMar();
        $updateArr['status'] = 0;
        $str = \app\laboratory\model\HostSilo::getSiloMarStr($updateArr['rmb_detail']);
        $where = [
            'station_code' => $updateArr['station_code'],
            'host_code' => $updateArr['host_code'],
            'silo_mar' => $str['silo_mar']
        ];
        $updateArr['silo_mar_id'] = $siloMar->where($where)->value('silo_mar_id');
        if (!$updateArr['silo_mar_id']) {
            error('siloMar error',[],'2');
        }
        $updateArr['update_user'] = $this->userId;
    }

    /**
     * @param $updateArr
     * @param $oldData
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData)
    {
        $this->LoadLang('RmbDetail');
        $RmbDetail = new RmbDetail();
        $id = $oldData['rmb_id'];
        array_walk($updateArr['rmb_detail'], function (&$value, $key, $id) {
            $value['rmb_id'] = $id;
        }, $id);
        $validate = new \app\laboratory\validate\RmbDetail();
        foreach ($updateArr['rmb_detail'] as $k => $v) {
            if (!$validate->check($v)) {
                error($validate->getError(), '', 2);
            }
        }
        $RmbDetail->where(['rmb_id'=>$id])->delete();
        $allField = $RmbDetail->allowFieldAdd ?? $RmbDetail->allowField;
        $RmbDetail->allowField($allField)->saveAll($updateArr['rmb_detail']);
    }

    /**
     * @param $where
     * @param $model
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where, &$model)
    {
        $model = $model->with('rmbDetail');
    }

    /**
     * @param $data
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data)
    {

    }

    /**
     * @param $deleteData
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 删除前置
     */
    public function _before_del(&$deleteData)
    {

    }

    /**
     * @param $deleteData
     * @author :
     
     * @date: 2020-12-24 09:24:43
     * @name: 删除后置
     */
    public function _end_del(&$deleteData)
    {

    }

    /**
     * @param $data
     * @author : 萤火虫
     
     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data)
    {

    }

    /**
     * @param $deleteData
     * @author :
     
     * @date: 2020-12-18 15:23:17
     * @name: 删除后置
     */
    public function _before_delAll(&$deleteData)
    {

    }

    protected function checkRecipe($data){

        $config=\app\admin\logic\Config::getConfig(['laboratory']);
        //开启自动审核1  不开启 0   无需审核-1
        $config['laboratory']=array_flip($config['laboratory']);
        if($config['laboratory']['auto_check']==1){
            $count=[];
            $rmb_detail=$data['rmb_detail'];
            foreach ($rmb_detail as $k=>$v){
                $count[$v['material_code']]=isset($count[$v['material_code']])?($count[$v['material_code']]+$v['recipe']):$v['recipe'];
            }
            $Recipe = new \app\laboratory\model\Recipe();
            $where=[
                ['host_code','=',$data['host_code']],
                ['station_code','=',$data['station_code']],
                ['material_code','=',$data['product_code']]
            ];
            $recipe=$Recipe->where($where)->with("RecipeBase")->order('update_time desc')->find();
            if($recipe){
                if(count($recipe['recipe_base'])==count($count)){
                    $pass=1;
                    foreach ($recipe['recipe_base'] as $k=>$v){
                        $count[$v['material_code']]=$count[$v['material_code']]??0;
                        if((($v['material_num']+$v['float_up'])<$count[$v['material_code']]) ||
                            (($v['material_num']-$v['float_down'])>$count[$v['material_code']]) ){
                            $pass=0;
                        }
                    }
                }
            }
        }
        if($config['laboratory']['auto_check']==-1){
            $pass=1;
        }
        return $pass??0;
    }
}
