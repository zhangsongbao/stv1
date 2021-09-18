<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/10/28 10:56
 * @name:
 */
namespace  hnzl\model;
use hnzl\HnzlModel;
class AuthGroupAccess extends HnzlModel{
    /**
     * @param $userId
     * @return array|mixed
     * @author : yhc
     
     * @date: 2020/10/29 09:38
     * @name:获取用户角色id
     */
    public static function UserGroupIds($userId){
        $cacheKey='Hnzl_AuthGroupAccess'.$userId;
        if(!$AuthGroupAccess=redis($cacheKey)){
            $AuthGroupAccess=self::where(['user_id'=>$userId])->column('group_id');
            redis($cacheKey,$AuthGroupAccess);
        }
        return $AuthGroupAccess;
    }
    public static function  groupUser(int $groupId,$field='*',$where=[],$with=false){
        $default[]=['group_id','=',$groupId];
        $where=array_merge($default,$where);
        $groupUser=self::where($where)->column('user_id');

        if(!empty($field) && $groupUser){
            if($with){
                $groupUser=User::where([
                    ['is_delete','=','0'],
                    ['id','in',$groupUser]
                ])->field($field)->with($with)->select();
                if($groupUser){
                    $groupUser=$groupUser->toArray();
                }
            }else{
                $groupUser=User::where([
                    ['is_delete','=','0'],
                    ['id','in',$groupUser]
                ])->field($field)->select();
            }

        }
        return $groupUser;
    }

}