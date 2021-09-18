<?php
/**
 * @author : yhc

 * @date: 2020/10/17 上午10:52
 * @name:
 */
namespace app\admin\controller;
use hnzl\HnzlController;
use hnzl\model\AuthGroupAccess;
use think\Db;
use think\Exception;

class  AuthGroup extends HnzlController
{
    public  $field='id,pid,name,rules,status,id as group_id';
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['getGroup'];
    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model=model('hnzl\model\AuthGroup');
        //载入验证器
        $this->validate=new \app\admin\validate\AuthGroup();
    }
    public function _before_add(&$insert){
        //
        $this->checkRole($insert['pid'],true);
    }

    protected function _before_edit(&$update,$oldData){
        //判断有没有下级
        if($childArrays=$this->model->getChildrenIds($oldData['id'])){
            if(in_array($update['pid'],$childArrays)){
                error(__('pid error'));
            }
        }
        $this->checkRole($oldData['id'],false);
    }
    protected function checkRole($id,$with){
        $ids=$this->model->getChildrenGroupIds($this->userId,$with);
        if(!in_array($id,$ids)){
            error(__('Auth Error'));
        }
    }
    /**
     * @param $where
     * @param $model
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where,&$model,$with=true){
        $model=$model->field($this->field)->withCount('user');
    }
    /**
     * @param $deleteData
     * @author : yhc

     * @date: 2020/11/05 15:57
     * @name: 删除前置
     */
    protected function _before_del($deleteData){
        //判断有没有下级
        $Childs=$this->model->where('pid',$deleteData->id)->value('id');
        if($Childs){
            error(__('has child'));
        }
        $user=AuthGroupAccess::groupUser($deleteData->id);
        if(count($user)>0){
            error(__('has user',['s'=>count($user)]));
        }
    }


    /**
     * @param $data
     * @author : yhc

     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data){

    }

    /**
     * @author : yhc

     * @date: 2020/11/25 10:07
     * @name: 根据角色按站点查看用户
     */
    public function groupUserView(){
        $groupId=$this->request->post('group_id');
        //获取用户数据
        $chilcUserIds=$this->model->getChildrenUserIds($this->userId,true);
        $where=[
            ['user_id','in',$chilcUserIds]
        ];
        $users=AuthGroupAccess::groupUser($groupId,'id,real_name',$where,['userStation']);
        $stationUser=[];
        foreach ($users as $k=>$v){
            if(isset($v['user_station'])){
                foreach ($v['user_station'] as $ks=>$vs){
                    $stationUser[$vs['user_station_id']][]=[
                        'id'=>$v['id'],
                        'real_name'=>$v['real_name']
                    ];
                }
            }
        }
        //获取所有站点
        $stations=\app\admin\model\Station::stations();
        foreach ($stations as $k=>$v){
            $stationUser[$k]=$stationUser[$k]??[];
            $stations[$k]=[
                'users'=>$stationUser[$k],
                'station'=>$v,
                'count'=>$stationUser[$k]
            ];
        }
        //用户站点
        success(__('Success'),['stationUser'=>$stations,'countUser'=>count($users)]);
    }
    /**
     * @author : yhc

     * @date: 2020/11/09 16:22
     * @name: 获取用户信息
     */
    public  function groupUser(){
        $groupId=$this->request->post('group_id');
        $getData=$this->request->post('getData');
        //获取用户数据
        $chilcUserIds=$this->model->getChildrenUserIds($this->userId,true);

        $where=[];
        if(!$this->model->isSupperUser($this->userId)){
            $where=[
                ['user_id','in',$chilcUserIds]
            ];
        }
        if($getData){
            if(empty($groupId)){
                error(__('Require',['s'=>__("id")]));
            }
            $users=AuthGroupAccess::groupUser($groupId,'id,real_name',$where);
            success(__('Success'),$users);
        }
        //提交用户数据
        $userIds=$this->request->post('user_id');
        Db::startTrans();
        try{
            $where[]=['group_id','=',$groupId];
            $oldData= AuthGroupAccess::where($where)->select();
            AuthGroupAccess::where($where)->delete();
            if(!empty($userIds)){
                $userIds=explode(',',$userIds);
                if(count($userIds)>0){
                    $inserts=[];
                    foreach ($userIds as $k=>$v){
                        $inserts[]=[
                            'user_id'=>$v,
                            'group_id'=>$groupId
                        ];
                    }
                    AuthGroupAccess::insertAll($inserts);
                }
            }

            logs()->log(['oldData'=>$oldData,'newData'=>$inserts??[]]);
            //清空用户分组缓存
            redis('Hnzl_Auth*',null);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            error($e->getMessage());
        }
        success(__('Success'));
    }

    protected function _end_edit($updateArr,$oldData){

        redis('Hnzl_Auth*',null);
    }

    protected function _end_add($insert){
        //添加用户分组   清除分组缓存即可
        redis('Hnzl_Auth*',null);
    }
    public function getGroup(){
        $data=$this->model->groups('id,name,pid',$this->userId,false);
        success(__('success'),array_values($data));
    }
}