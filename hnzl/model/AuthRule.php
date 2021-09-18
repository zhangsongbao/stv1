<?php
/**
 * Created by PhpStorm.
 * @author : yhc
 
 * @date: 2020/10/28 10:56
 * @name:
 */
namespace  hnzl\model;
use hnzl\HnzlModel;

class AuthRule extends HnzlModel{
    /**
     * @param string $userId 用户id
     * @param string $url  路径
     * @return bool
     * @author : yhc
     
     * @date: 2020/10/29 09:38
     * @name: 检查用户权限
     */
    public static function checkRole($userId='',$url=''){

        $userId=$userId?:auth()->user('id');
        $url=$url?:request()->path();

        $url= self::urlChange(strtolower($url));
        $id=self::ruleId($url);

//        if(!$id){
//            //不在菜单内 禁止访问
//            return false;
//        }
        //超级用户组
        if(AuthGroup::isSupperUser($userId)){
            return true;
        }
        $userGroupIds=AuthGroupAccess::UserGroupIds($userId);
        $userRole=AuthGroup::UserRole($userId,$userGroupIds);

        $res=false;
        if($id && in_array($id,$userRole)){
            $res=true;
        }
        return $res;
    }
    public  static function ruleId($url){
        $cacheKey='hnzl_AuthRule.'.$url;

        if(!$id=redis($cacheKey)){
            $where=[
                ['status','=',0],
                ['name','=',$url]
            ];
            $id=self::where($where)->value('id');

            redis($cacheKey,$id);
        }
        return $id;
    }

    /**
     * @param $url
     * @return mixed
     * @author : yhc
     
     * @date: 2020/12/29 10:15
     * @name: 权限重定向 解决同权问题
     */
    public static function urlChange($url){
        $arr=[
            //添加编辑生产配比时选择
            'laboratory/recipe/select'=>'laboratory/recipeMaterialBase',
            //添加编辑生产配比时选择
            'laboratory/hostSilo/hostSilos'=>'laboratory/recipeMaterialBase',
        ];
        return $arr[$url]??$url;
    }
}