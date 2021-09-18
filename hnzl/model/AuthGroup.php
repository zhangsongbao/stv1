<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/10/28 10:56
 * @name:
 */
namespace  hnzl\model;
use hnzl\HnzlModel;
use hnzl\inc\Tree;

class AuthGroup extends HnzlModel
{
    public function user(){
        return $this->hasMany('AuthGroupAccess','group_id','id');
    }
    /**
     * @param string $userId
     * @return array|mixed
     * @author : yhc
     
     * @date: 2020/10/29 09:37
     * @name: 获取用户所有权限id
     */
    public static function UserRole($userId,$userGroupIds=[]){
        $userId=$userId?:auth()->user('id');
        $cacheKey='Hnzl_AuthUserRole'.$userId;
        if(!$userRoles=redis($cacheKey)){
            $userGroupIds=count($userGroupIds)>0?$userGroupIds:AuthGroupAccess::UserGroupIds($userId);
            $userRoles=[];
            if(count($userGroupIds)>0){
                $userGroup=self::where(['id'=>['in',$userGroupIds],'status'=>['=',0]])->field('rules')->select();
                $userRoles=[];
                foreach ($userGroup as $k=>$v){
                    if(!empty($v['rules'])){
                        $userRoles=array_merge($userRoles,json_decode($v['rules'],true));
                    }
                }
            }
            redis($cacheKey,$userRoles);
        }
        return $userRoles;
    }

    /**
     * @param null $userId
     * @return mixed|\think\Model[]
     * @author : yhc
     
     * @date: 2020/11/02 15:37
     * @name: 获取用户所有分组
     */
    public static function userGroups($userId=null){
        $cacheKey='hnzl_AuthGroups'.$userId;
        if(!$userGroups=redis($cacheKey)){
            if($userId!=null){
                $userGroupIds=AuthGroupAccess::UserGroupIds($userId);
                $where['id']=['in',$userGroupIds];
            }
            $where['status']=['=','0'];
            $userGroups=self::where($where)->field('id,pid,name,rules')->select();
            if($userGroups){
                $userGroups=$userGroups->toArray();
            }
            redis($cacheKey,$userGroups);
        }
        return $userGroups;
    }

    /**
     * @param string $field
     * @param null $userId
     * @return array|bool|mixed
     * @author : yhc
     
     * @date: 2020/11/25 10:46
     * @name: 获取用户分组
     */
    public static function groups($field='id,name',$userId=null,$with=true){
        $userId=isset($userId)?$userId:auth()->user('id');
        $cacheKey='hnzl:AuthGroupsALl:'.md5($field.$userId.$with);

        if(!$userGroups=redis($cacheKey)){
            if($userId){
                $userGroups=self::where('id','in',self::getChildrenGroupIds($userId,$with))->column($field);
            }else{
                $userGroups=self::column($field);
            }
            redis($cacheKey,$userGroups);
        }
        return $userGroups;
    }

    public static function getChildrenIds($groupId,$msg=false){

        $cacheKey='hnzl:AuthGroupsChildrenIds'.$groupId;
        if(!$userGroups=redis($cacheKey)){

            $where['status']=['=','0'];
            $where['pid']=['=',$groupId];
            $userGroups=self::where($where)->value('id');
            redis($cacheKey,$userGroups);
        }
        return $userGroups;

    }
    /**
     * @param null $userId
     * @param bool $withself
     * @param bool $groupMsg
     * @return array
     * @author : yhc
     
     * @date: 2020/11/02 10:59
     * @name:取出当前管理员所拥有权限的分组
     */
    public static function getChildrenGroupIds($userId=null,$withself = false,$groupMsg=false)
    {
        $cacheKey='hnzl:AuthGroupChildIds:'.$groupMsg.":".(int)$userId.'-'.$withself;

//        if($childrenGroupIds=redis($cacheKey)){
//            return  $childrenGroupIds;
//        }
        $userId=$userId?$userId:auth()->user('id');
        // 取出所有分组
        $groupList = self::userGroups();
        //取出当前管理员所有的分组
        if(self::isSupperUser($userId)){
            if($groupMsg==false){
                foreach ($groupList as $k => $v) {
                    $childrenGroupIds[] = $v['id'];
                }
            }
        }else{
            $groups = self::userGroups($userId);
            $groupIds = [];
            foreach ($groups as $k => $v) {
                $groupIds[] = $v['id'];
            }
            $originGroupIds = $groupIds;
            foreach ($groups as $k => $v) {
                if (in_array($v['pid'], $originGroupIds)) {
                    $groupIds = array_diff($groupIds, [$v['id']]);
                    unset($groups[$k]);
                }
            }
            $objList = [];
            foreach ($groups as $k => $v) {
                if ($v['rules'] === '*') {
                    $objList = $groupList;
                    break;
                }
                // 取出包含自己的所有子节点
                $childrenList = Tree::instance()->init($groupList)->getChildren($v['id'], $withself);
                $obj = Tree::instance()->init($childrenList)->getTreeArray($v['id']);
                $objList = array_merge($objList, Tree::instance()->getTreeList($obj));
            }
            $childrenGroupIds = [];
            if($groupMsg==false){
                foreach ($objList as $k => $v) {
                    $childrenGroupIds[] = $v['id'];
                }
                if (!$withself) {
                    $childrenGroupIds = array_diff($childrenGroupIds, $groupIds);
                }else{
                    $childrenGroupIds = array_merge($childrenGroupIds, $groupIds);
                }
            }else{
                foreach ($objList as $k => $v) {
                    $childrenGroupIds[$v['id']] = $v;
                }
                if (!$withself) {
                    foreach ($groups as $k=>$v){
                        if(isset($childrenGroupIds[$v['id']])){
                            unset($childrenGroupIds[$v['id']]);
                        }
                    }
                }
            }


        }
        redis($cacheKey,$childrenGroupIds);
        return $childrenGroupIds;
    }

    /**
     * @param null $userId
     * @param null $userGroupIds
     * @return bool
     * @author : yhc
     
     * @date: 2020/11/09 16:13
     * @name: 是否为超级管理
     */
    public static function isSupperUser( $userId=null,array $userGroupIds=null){
        $userId=isset($userId)?$userId:auth()->user('id');
        $userGroupIds=isset($userGroupIds)?$userGroupIds:AuthGroupAccess::UserGroupIds($userId);
        //超级用户组
        if(in_array(1,$userGroupIds)){
            return true;
        }
        return false;
    }

    /**
     * @param boolean $withself 是否包含自身
     * @return array
     * @author : yhc
     
     * @date: 2020/11/02 16:05
     * @name: 取出当前管理员所拥有权限的管理员
     */
    public static function getChildrenUserIds($withself = false)
    {
        $childrenAdminIds = [];
        if (!self::isSupperUser()) {
            $groupIds =self::getChildrenGroupIds($withself);
            $authGroupList = \hnzl\model\AuthGroupAccess::field('user_id,group_id')
                ->where('group_id', 'in', $groupIds)
                ->select();
            foreach ($authGroupList as $k => $v) {
                $childrenAdminIds[] = $v['user_id'];
            }
        } else {
            //超级管理员拥有所有人的权限
            $childrenAdminIds = \hnzl\model\User::column('id');
        }
        $userId=auth()->user('id');
        if ($withself) {
            if (!in_array($userId, $childrenAdminIds)) {
                $childrenAdminIds[] = $userId;
            }
        } else {
            $childrenAdminIds = array_diff($childrenAdminIds, [$userId]);
        }
        return $childrenAdminIds;
    }
}