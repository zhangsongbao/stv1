<?php
namespace app\admin\model;
use hnzl\HnzlModel;
use hnzl\model\AuthRule;

/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-13 09:04:07
 */
class Attachment  extends HnzlModel{
    public $allowField=true;
    public $softDelete=false;

    /**
     * @param $id
     * @return bool|mixed
     * @author : yhc
     
     * @date: 2020/11/25 17:21
     * @name: 获取id对应内容url
     */
    public static function file($id){
        $cacheKey='Admin:Attachment:'.$id;
        if(!$file=redis($cacheKey)){
            $file=self::where('id',$id)->value('url');
            redis($cacheKey,$file);
        }
        return $file;
    }
    public static function fileMsg($id){
        $cacheKey='Admin:AttachmentMsg:'.$id;
        if(!$file=redis($cacheKey)){
            $file=self::where('id',$id)->find();
            redis($cacheKey,$file);
        }
        return $file;
    }
    public function user(){
        return $this->hasOne('hnzl\model\User', 'id', 'user_id')->bind(['real_name','mobile']);
    }
    /**
     * @author : 萤火虫
     
     * @date: 2021-3-25 下午5:38
     * @name: 清除文件
     */
    public static function delFile($id,$type='id'){
        if($type=='id'){
            //附件id
            $params=self::fileMsg($id);
        }
        //判断权限
        $roleUrl=$params['admin'].$params['use_module'].'/'.$params['use_controller'].'/'.$params['use_action'];
        $noNeed=['tmp/tmp/tmp','admin/user/userMsg'];
        if(!in_array($roleUrl,$noNeed)){
            if(!AuthRule::checkRole(auth()->id,$roleUrl)){
                return false;
            }
        }
        if($params){
            unlink($params['url']);
            self::where('id',$id)->delete();
        }
        if($params['user_id']){
            \app\admin\model\UserFile::des($params['user_id'],$params['filesize']);
        }
        return true;
    }
}