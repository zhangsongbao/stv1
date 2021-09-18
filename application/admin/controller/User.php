<?php
/**
 * @author : yhc
 
 * @date : 2020/10/10 8:52
 * @name :
 */

namespace app\admin\controller;
use app\admin\model\Station;
use app\admin\model\UserStation;
use app\admin\model\UserType;
use hnzl\HnzlController;
use hnzl\model\AuthGroup;
use hnzl\model\AuthGroupAccess;
use think\Db;

class User extends HnzlController
{
    protected $noNeedLogin = ['login','vue','text'];
    protected $noNeedRight = ['getUser','userMsg','selectUser','selectAllUser'];
    public $childrenGroupIds=[];
    public $ChildrenUserIds=[];
    public $field='id,user_name,real_name,mobile,status,weigh,avater';
    protected $import = [
        'limit' => '1000',
        'type' => 'saveAll',
    ];
    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model=model('hnzl\model\User');
        $this->validate=new \app\admin\validate\User();
        $with=false;

        if($this->userId){
            if(AuthGroup::isSupperUser($this->userId)){
                $with=true;
            }

            $this->childrenGroupIds=AuthGroup::getChildrenGroupIds($this->userId,$with);
            $this->ChildrenUserIds=AuthGroup::getChildrenUserIds($with);
        }
    }

    public function text(){
        $user =new \hnzl\model\User();
        $res=Db::name('user')->alias('u')
            ->join('user_file uf','u.id=uf.user_id')
            ->select();

        $res=$user
            ->with('userType')
            ->withSum(['userType'=>'total'],'user_type_id')
            ->field('hnzl_user.*')
            ->limit(1,2)
            ->group('id')
            ->select();

//        $dept->with(['user'=>function($query){
//            $query->order('user_id desc');
//        }])->order('dept_id  desc')->select();
//
//        $user->with(['dept'=>function($query){
//            $query->order('dept_id  desc');
//        }])->order('user_id desc')->select();
       // select * from user a,department b where a.dept_id=b.id order by b.no desc a.id

        //$user->withSum('cards','total')->select([1,2,3]);

        var_dump($res);exit;

    }
    /**
     * @author : yhc
     
     * @date: 2020/11/10 09:37
     * @name: 用户列表/带权限
     */
    public  function  getUser(){
        $where=[
            ['id','in',$this->ChildrenUserIds]
        ];
        success(__('Success'),$this->model->users($where));
    }
    public function users(){
        success(__('Success'),$this->model->users());
    }
    public function _before_index(&$where,&$model,$with=true){
        //$model=$model->field($field);
        $ids=$this->ChildrenUserIds;
        //一对多 采用预载入查询   直接改变
        foreach ($where as $k=>$v){
            //角色查询
            if($v[0]=='user_group'){
                if(!empty($v[2])){
                    $ids=array_intersect($ids,AuthGroupAccess::where('group_id',$v[1],$v[2])->column('user_id'));
                }
                unset($where[$k]);
            }
            //type类型查询
            if($v[0]=='user_type'){
                if(!empty($v[2])){
                    $ids=array_intersect($ids,UserType::where('user_type_id',$v[1],$v[2])->column('user_id'));
                }
                unset($where[$k]);
            }
            //站点查询
            if($v[0]=='user_station'){
                if(!empty($v[2])){
                    if(!in_array($v[2],auth()->stations())){
                        $ids=[];
                        //无权限 跳出循环即可
                        continue;
                    }
                    $searchStations=[
                        'station'=>$v[2],
                        'op'=>$v[1]
                    ];
                }
                unset($where[$k]);
            }
        }
        //重排序
        $where=array_merge($where);
        //用户id 需要在管理的站点内 才能进行查看
        if(!AuthGroup::isSupperUser($this->userId) && !isset($searchStations)){
            $searchStations=[
                'station'=>auth()->stations(),
                'op'=>'in'
            ];
        }
        if(isset($searchStations)){
            $ids=array_intersect($ids,UserStation::where('user_station_id',$searchStations['op'],$searchStations['station'])->column('user_id'));
        }
        if(empty($ids)){
            $where[]=['id','<',0];
        }else{
            //只能看到下属人id
            $where[]=['id','in',array_intersect($ids,$this->ChildrenUserIds)];
        }
        if($with==true){
            $with=['userType','userStation','userGroup','userAvater'];
            $model=$model->field($this->field)->with($with);
        }
        //$model=$model->alias('u')->field($field)->leftJoin('auth_group_access ags','hnzl_user.id=ags.user_id');
    }
    public function vue(){
        header('Location:./demo/');
        exit;
    }
    public function login(){

        try{
            $user_name=$this->request->post('user_name');
            $password=$this->request->post('password');
            if(empty($user_name)){
                throw new \Exception(__('Require',['s'=>__('user_name')]));
            }
            $where=[
                'user_name|mobile'=>$user_name,
                'is_delete'=>0
            ];
            $user=$this->model->where($where)->field('id,user_name,real_name,password,salt,mobile,status')->find();
            if(!$user){
                throw new \Exception(__('User Not Exists'));
            }
            $password=$this->model->getPassword($password,$user['salt']);
            if($password!=$user['password']){
                logs()->log(['login'=>$user,'msg'=>__('Login Error')],10,$user);
                throw new \Exception(__('Login Error'));
            }
            if($user['status']=='1'){
                logs()->log(['login'=>$user,'msg'=>__('Login Forbid')],10,$user);
                throw new \Exception(__('Login Forbid'));
            }
        }catch(\Exception $e){
            error($e->getMessage(),'',2);
        }
        $token=auth()->getToken($user);
        logs()->log(['login'=>$user],0,$user);
        success('success',['token'=>$token,'refresh_token'=>'']);
    }
    protected function _before_add(&$insert){
        //检查是否有当前手机号/用户名
        if(isset($insert['password']) &&!empty($insert['password'])){
            $insert=array_merge($insert,$this->model->getPassword($insert['password']));
        }else{
            $insert['password']='';
        }
        $insert['is_delete']=0;
        if(isset($insert['id']))unset($insert['id']);
    }
    protected function _end_add($insert){
        //添加站点、用户类型,用户角色
        $this->model->userChange($insert);
        //添加用户上传信息
        task('app\admin\logic\UserFile','add',$insert['id']);
    }
    protected function _before_edit(&$update,$oldData){
        $update['is_delete']=0;
        //密码更新
        if(!empty($update['password'])){
            $update=array_merge($update,$this->model->getPassword($update['password']));
        }
        if(!in_array($oldData->id,$this->ChildrenUserIds)){
            error(__('Auth Error'));
        }
        if($oldData->id==$this->userId){
            //不允许修改自己的用户类型、站点,角色
            if(!AuthGroup::isSupperUser($this->userId)){
                $unset=['user_type,user_station,auth_group'];
                foreach ($unset as $k=>$v){
                    if (isset($update[$v])){
                        unset($update[$v]);
                    }
                }
            }
        }
    }
    protected function _end_edit($updateArr,$oldData){
        $this->model->userChange($updateArr,true);
        redis('hnzl:AuthUserMenu'.$updateArr['id'],null);
        redis('hnzl:AuthGroupAccess'.$updateArr['id'],null);
        redis('hnzl:AuthUserRole'.$updateArr['id'],null);
    }
    protected function _end_view(&$data){
        $data->userType;
        $data->userStation;
        $data->userGroup;
        if(!in_array($data->id,$this->ChildrenUserIds)){
            error(__('Auth Error'));
        }
    }
    protected  function _before_view(&$where,&$model){
        $model=$model->field($this->field);
    }
    protected function _before_del(&$data){
        //超管和不在子集内 都不允许删除
        if($data->id==1){
            error(__('Auth Error'));
        }
        if(!AuthGroup::isSupperUser($this->userId)){
            if(!in_array($data->id,$this->ChildrenUserIds)){
                error(__('Auth Error'));
            }
        }
        redis('Hnzl_Auth*',null);
        //删除用户角色
        AuthGroupAccess::where('user_id',$data->id)->delete();
    }

    protected function _before_delAll(&$data){
        redis('Hnzl_Auth*',null);
    }

    public function resetPassword(){
        $this->editAll(['password'=>'']);
    }
    public function status(){
        $this->editAll();
    }
    public function userMenu(){
        success(__('Success'),auth()->UserMenu());
    }
    protected function import_data($data){
        $config=\app\admin\logic\Config::getConfig(['user_type']);
        $config['user_group']=AuthGroup::groups();
        foreach ($config as $k=>$v){
            $config[$k]=array_flip($v);
        }
        $config['user_station']=Station::stations('name,value');
        Db::startTrans();
        $userTypeModel=new UserType();
        $userStationModel=new UserStation();
        $userGroupModel=new AuthGroupAccess();
        try {
            foreach ($data as $k => $v) {
                $tmp = array_merge($v, $this->model->getPassword($v['password']));
                $tmp['status'] = $tmp['status'] == '启用' ? 0 : 1;
                $userType = explode(',', $tmp['user_type']??'');
                $userStation = explode(',', $tmp['user_station']??'');
                $userGroup = explode(',', $tmp['user_group']??'');
                unset($tmp['user_type']);
                unset($tmp['user_station']);
                unset($tmp['user_group']);
                $insertUsers[] = $tmp;
                $userTypes=$userStations=$userGroups=[];
                if (!$this->validate->scene('add')->check($tmp)) {
                    throw new Exception(__('line',['s'=>$k+2]).$this->validate->getError());
                }
                $uid = $this->model->insertGetId($tmp);
                foreach ($userType as $vs) {
                    if (!isset($config['user_type'][$vs])) {
                        throw new Exception(__('line',['s'=>$k+2]).$vs.__('Not Exists'));
                    }
                    $userTypes[] = ['user_id' => $uid, 'user_type_id' => $config['user_type'][$vs]];
                }

                foreach ($userStation as $vs) {
                    if (!isset($config['user_station'][$vs])) {
                        throw new Exception(__('line',['s'=>$k+2]).$vs.__('Not Exists'));
                    }
                    $userStations[] = ['user_id' => $uid, 'user_station_id' =>$config['user_station'][$vs]];
                }
                foreach ($userGroup as $vs) {
                    if (!isset($config['user_group'][$vs])) {
                        throw new Exception(__('line',['s'=>$k+2]).$vs.__('Not Exists'));
                    }
                    $userGroups[] = ['user_id' => $uid, 'group_id' => $config['user_group'][$vs]];
                }
                if(isset($userGroups)){
                    if(!$userGroupModel->insertAll($userGroups)){
                        throw new Exception(__('line',['s'=>$k+2]).__('user_group').__('Add Error'));
                    }
                }
                if(isset($userStations)) {
                    if (!$userStationModel->insertAll($userStations)) {
                        throw new Exception(__('line', ['s' => $k + 2]) . __('user_station') . __('Add Error'));
                    }
                }
                if(isset($userTypes)) {
                    if (!$userTypeModel->insertAll($userTypes)) {
                        throw new Exception(__('line', ['s' => $k + 2]) . __('user_type') . __('Add Error'));
                    }
                }
            }
            task('app\admin\logic\UserFile','add',$uid);
            Db::commit();
        }catch (Exception $e){
            Db::rollback();
            error($e->getMessage());
        }
        success(__('Import Success'));
    }

    /**
     * @param $data
     * @author : yhc
     
     * @date: 2020/11/24 11:24
     * @name:格式化数据
     */
    protected function _formate(&$data){
        $config=\app\admin\logic\Config::getConfig(['user_type']);
        $config['user_group']=AuthGroup::groups();
        $config['user_station']=Station::stations('value,name');
        foreach ($data as $k=>$v){
            $data[$k]['user_type']=codeToText($v['user_type'],$config['user_type'],'user_type_id');
            $data[$k]['user_station']=codeToText($v['user_station'],$config['user_station'],'user_station_id');
            $data[$k]['user_group']=codeToText($v['user_group'],$config['user_group'],'group_id');
        }
    }

    /**
     * @author : yhc
     
     * @date: 2020/11/24 11:23
     * @name: 获取用户信息
     */
    public function userMsg(){
        $getData=$this->request->post('getData',0);
        $with=['userType','userStation','userGroup','userAvater'];
        $model=$this->model->field($this->field.',password,salt')->with($with)->where('id',$this->userId)->find()->toArray();
        if($getData==1){
            unset($model['salt'],$model['password']);
            $config=\app\admin\logic\Config::getConfig(['user_type']);

            $config['user_group']=AuthGroup::groups();


            $config['user_station']=Station::stations('value,name');
            print_r($model['user_type']);die;

            if($model['user_type']){
                foreach ($model['user_type'] as $k=>$v){
                    $model['user_type'][$k]['name']=$config['user_type'][$v['user_type_id']]??"";
                }
                foreach ($model['user_group'] as $k=>$v){
                    $model['user_group'][$k]['name']=$config['user_group'][$v['group_id']]??"";
                }
                foreach ($model['user_station'] as $k=>$v){
                    $model['user_station'][$k]['name']=$config['user_station'][$v['user_station_id']]??"";
                }
            }
            logs()->log(['viewData' => $model], 4);
            success(__('Success'),$model);
        }else{
            $updateArr = $this->request->post();
            $updateArr['id']=$this->userId;
            if ($this->validate) {
                if (!$this->validate->scene('edit')->check($updateArr)) {
                    error($this->validate->getError(), '', '2');
                }
            }
            if(isset($updateArr['password'])){
                $oldPassword=$this->model->getPassword($updateArr['old_password'],$model['salt']);
                if($oldPassword!=$model['password']){
                    error(__('old_password error'));
                }
                if(empty($updateArr['password'])){
                    unset($updateArr['password']);
                }else{
                    $updateArr=array_merge($updateArr,$this->model->getPassword($updateArr['password']));
                }
            }
            Db::startTrans();
            try{
                $where=[['id','=',$this->userId]];
                $this->model->allowField('real_name,mobile,password,salt,avater')->isUpdate(true)->save($updateArr);
                //$this->model->userChange($updateArr,true);
                logs()->log(['oldData' => [], 'update' => $updateArr, 'where' =>$where],3);
                Db::commit();
            }catch (Exception $e){
                Db::rollback();
                error($e->getMessage());
            }
            success(__('Success'));
        }
    }

    /**
     * @author : yhc
     
     * @date: 2020/11/25 10:18
     * @name: 按站点/角色查看用户  包含权限
     */
    public function  selectUser(){
        //获取用户数据
        $with=['userStation','userGroup'];
        $wheres=[
            ['id','in',$this->ChildrenUserIds],
            ['is_delete','=','0']
        ];
        $users=$this->model->where($wheres)->field('id,real_name')->with($with)->select();

        $stationUser=$groupUser=[];
        foreach ($users as $k=>$v){
            if(isset($v['user_station'])){
                foreach ($v['user_station'] as $ks=>$vs){
                    $stationUser[$vs['user_station_id']][]=[
                        'id'=>$v['id'],
                        'real_name'=>$v['real_name']
                    ];
                }
            }
            if(isset($v['user_group'])){
                foreach ($v['user_group'] as $ks=>$vs){
                    $groupUser[$vs['group_id']][]=[
                        'id'=>$v['id'],
                        'real_name'=>$v['real_name']
                    ];
                }
            }
        }
        //获取用户所有站点
        // $stations=UserStation::where('user_id',$this->userId)->column('user_station_id');
        //获取所有站点
        $stations=auth()->stations();
        if(!AuthGroup::isSupperUser($this->userId)){
            $userStations=[];
            foreach ($stations as $k=>$v){
                $userStations[$v]=$stationUser[$v]??[];
            }
        }else{
            $userStations=$stationUser;
        }
        //获取所有分组
        $groups=AuthGroup::groups('id,name,pid',false);
        $userGroups=[];
        foreach ($groups as $k=>$v){
            $groupUser[$k]=$groupUser[$k]??[];
            $userGroups[]=[
                'group_id'=>$k,
                'group'=>$v['name'],
                'count'=>count($groupUser[$k]),
                'pid'=>$v['pid'],
                'users'=>$groupUser[$k],
            ];
        }
        $config=[
            'station'=>Station::stations(),
            'group'=>$groups
        ];

        //用户站点
        success(__('Success'),['stationUser'=>$userStations,'groupUser'=>$userGroups,'count'=>count($users),'config'=>$config]);
    }


    /**
     * @author : yhc
     
     * @date: 2020/11/25 10:18
     * @name: 按站点/角色查看用户  不包含权限
     */
    public function  selectAllUser(){
        //获取用户数据
        $with=['userStation','userGroup'];

        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $wheres[]=[
            ['is_delete','=','0']
        ];
        $users=$this->model->where($wheres)->field('id,real_name')->with($with)->select();

        $stationUser=$groupUser=[];
        foreach ($users as $k=>$v){
            if(isset($v['user_station'])){
                foreach ($v['user_station'] as $ks=>$vs){
                    $stationUser[$vs['user_station_id']][]=[
                        'id'=>$v['id'],
                        'real_name'=>$v['real_name']
                    ];
                }
            }
            if(isset($v['user_group'])){
                foreach ($v['user_group'] as $ks=>$vs){
                    $groupUser[$vs['group_id']][]=[
                        'id'=>$v['id'],
                        'real_name'=>$v['real_name']
                    ];
                }
            }
        }
        //获取用户所有站点
        // $stations=UserStation::where('user_id',$this->userId)->column('user_station_id');
        //获取所有站点
        $stations=auth()->stations();
        if(!AuthGroup::isSupperUser($this->userId)){
            $userStations=[];
            foreach ($stations as $k=>$v){
                $userStations[$v]=$stationUser[$v]??[];
            }
        }else{
            $userStations=$stationUser;
        }
        //获取所有分组
        $groups=AuthGroup::groups('id,name,pid',false);
        $userGroups=[];
        foreach ($groups as $k=>$v){
            $groupUser[$k]=$groupUser[$k]??[];
            $userGroups[]=[
                'group_id'=>$k,
                'group'=>$v['name'],
                'count'=>count($groupUser[$k]),
                'pid'=>$v['pid'],
                'users'=>$groupUser[$k],
            ];
        }
        $config=[
            'station'=>Station::stations(),
            'group'=>$groups
        ];

        //用户站点
        success(__('Success'),['stationUser'=>$userStations,'groupUser'=>$userGroups,'count'=>count($users),'config'=>$config]);
    }

    /**
     * @author : yhc
     
     * @date: 2021/01/04 17:41
     * @name: 站点用户分组
     */
    public function stationUserAll(){
        $UserStation=new UserStation();
        $users=$UserStation->where('user_station_id','in',auth()->stations())->with('user')->select();
        success(__('Success'),['users'=>$users,'stations'=>Station::stations($field='value,name',$this->userId)]);
    }

}