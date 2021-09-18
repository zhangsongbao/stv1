<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/07 11:16
 * @name: 获取所有配置信息
 */
namespace  app\admin\logic;
use app\admin\model\Attachment as AttachmentMd;
class Attachment{
    public static function file($id){
        return AttachmentMd::file($id);
    }

    /**
     * @param $key
     * @param $ids
     * @return bool
     * @author : yhc
     * @email : 445727994@qq.com
     * @date: 2021/8/6 11:56
     * @name: 修正附件问题
     */
    public static function checkAttachments($key,$ids){
        if(is_string($ids)){
            $ids=explode(',',$ids);
        }
        $keyIds=AttachmentMd::where(['key'=>$key])->column("filesize,user_id,url,use_module,use_controller,use_action",'id');
        //比较  查看删除
        $delIds=[];
        $hasTmp=[];
        foreach ($keyIds as $k=>$v){
            $url=$v['use_module'].$v['use_controller'].$v['use_action'];
            if(!in_array($k,$ids)){
                // 只删除tmp  和本模块下的数据 禁止删除其他模块附件
                if(is_file($v['url'])){
                    unlink($v['url']);
                }
                if($url=='tmptmptmp' || $url=request()->module().lcfirst(request()->controller()).request()->action(true)){
                    $delMsg[$v['user_id']]=isset( $delMsg[$v['user_id']])? $delMsg[$v['user_id']]+$v['filesize']:$v['filesize'];
                    $delIds[]=$k;
                }
            }else{
                //纠正tmp目录
                if($url=='tmptmptmp'){
                    $hasTmp[$k]=$v;
                }
            }
        }
        if(isset($delMsg)){
            foreach ($delMsg as $k=>$v){
                \app\admin\model\UserFile::des($k, $v['filesize']);
            }
        }
        if(!empty($delIds)){
            AttachmentMd::where(['id'=>['in',$delIds]])->delete();
        }
        if(!empty($hasTmp)){
            $url='/'.request()->module().'/'.lcfirst(request()->controller()).'/'.request()->action(true).'/';
            foreach ($hasTmp as $k=>$v){
                redis(AttachmentMd::$cacheKey.$key,null);
                $newUrl=str_replace('/tmp/tmp/tmp/',$url,$v['url']);
                if(is_file($v['url'])){
                    mkdirs(substr($newUrl,0,strripos($newUrl, '/')).'/');
                    rename($v['url'], $v['url']);
                }
                AttachmentMd::where(['id'=>$k])->update([
                    'url'=>$newUrl,
                    'use_module'=>request()->module(),
                    'use_controller'=>lcfirst(request()->controller()),
                    'use_action'=>request()->action(true),
                    'is_used'=>1
                ]);
            }
        }

        return true;
    }
    public static function delFile($id){
        return AttachmentMd::delFile($id);
    }
}
