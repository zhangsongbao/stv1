<?php
/*
 * @auther 萤火虫
 * @email
 * @create_time 2020-11-11 08:59:52
 */

namespace app\admin\controller;

use hnzl\HnzlController;
use think\Db;
use think\Exception;
use app\admin\model\UserFile;

class Attachment extends HnzlController
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
    protected $noNeedRight = ['download', 'upload'];

    public function _initialize()
    {

        parent::_initialize();
        //载入model
        $this->model = model('app\admin\model\Attachment');
        //载入验证器
        $this->validate = new \app\admin\validate\Attachment();
    }


    /**
     * @param $updateArr
     * @param $oldData
     * @author : 萤火虫
     * @date: 2020-11-11 08:59:52
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData)
    {
        if (!\hnzl\model\AuthGroup::isSupperUser($this->userId)) {
            if ($this->userId != $oldData['user_id']) {
                error('Auth Error');
            }
        }
    }

    /**
     * @param $updateArr
     * @param $oldData
     * @author : 萤火虫
     * @date: 2020-11-11 08:59:52
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData)
    {

    }

    /**
     * @param $deleteData
     * @author : 萤火虫
     * @date: 2020-11-11 08:59:52
     * @name: 删除前置
     */
    public function _before_del(&$deleteData)
    {
        error();
    }

    /**
     * @param $data
     * @author : yhc
     * @date: 2020/11/19 11:45
     * @name: 格式化index 查询的数据
     */
    protected function _formate(&$data)
    {

    }

    /**
     * @param $fileType string 文件类型
     * @param $fileSize string 文件大小
     * @return float|int|string
     * @author : yhc
     * @date: 2021/2/2 9:47
     * @name: 检车文件是否符合规范
     */
    protected function checkFile($fileType, $fileSize)
    {
        // $userFile = \app\admin\model\UserFile::config($this->userId);
        $userFile = \app\admin\model\Config::config('upload', $value = 'name,value');
        if ($userFile) {
            //判断文件后缀
            if (!in_array($fileType, explode('|', $userFile['type']))) {
                error(__('type error'));
            }
            //判断文件max_size
            $userMaxSize = userFile::size($userFile['max_size']);
            if ($userMaxSize != '-1') {
                if ($fileSize > $userMaxSize) {
                    error(__('max_size'));
                }
            }
//            //判断用户使用数量
//            $userUsed = userFile::size($userFile['used']);
//            $userMaxUse = userFile::size($userFile['max_use']);
//            $updateSize = $userUsed + $fileSize;
//            if ($userMaxUse != '-1') {
//                if ($updateSize > $userMaxUse) {
//                    error(__('max_use'));
//                }
//            }
            return true;
        }
        return true;
    }

    /**
     * @param $base64
     * @param $uploadDir
     * @author : yhc
     * @date: 2021/2/2 9:50
     * @name: base64接收文件
     */
    public function base64($base64, $uploadDir, $fileName)
    {
        header("Content-Type: text/html; charset=utf-8");
        ///^(data:\s*[(image|application|text)]+\/+([\w\W]*);base64)/
        if (preg_match('/^(data:([\w\W]*);base64)/', $base64, $result)) {
            $base64Types = config()['base64types'];
            if (!isset($base64Types[$result[1]])) {
                error(__('base64_type_error'));
            }
            $file = [
                'size' => intval(strlen($base64) - (strlen($base64) / 8) * 2),
                'type' => isset($result[2]) ? $result[2] : "",
                'postfix' => $base64Types[$result[1]],
                'name' => $this->request->post('name', 'base64'),
                'file_name' => $fileName . '.' . $base64Types[$result[1]]
            ];

            if (!file_put_contents($uploadDir . $file['file_name'],
                base64_decode(str_replace($result[1] . ',', '', $base64)))) {
                error(__('save_error'));
            }
            return $file;
        }
        error(__('base64_type_error'));
    }
    /**
     * @author : yhc
     * @email : 445727994@qq.com
     * @date: 2021/7/29 14:41
     * @name: 增加提前获取key
     */
    public function uploadKey(){
        success(__('Success'),['key'=>hashids($this->userId) .uniqid(substr(md5(microtime()),8,16))]);
    }
    public function upload()
    {
        $file = $this->request->file('file');
        $base64 = $this->request->post('base64');

        $extparam = $this->request->post('ext', '');
        $key = $this->request->post('key');
        $is_used = (int)$this->request->post('is_used', '-1');
        $uid= hashids($this->userId);
        $fileName =$uid.uniqid(substr(md5(microtime()),8,16));
        if ($is_used == 0) {
            $modules = trim($this->request->post('use_path', 'use_path'), '/');

            $modules = explode('/', $modules);
            if (count($modules) != 3) {
                error(__('Require', ['s' => __('use_path')]));
            }
        } else {
            $modules = ['tmp', 'tmp', 'tmp'];
        }
        Db::startTrans();
        try {
            if ($this->request->post('save_type', 0) == 1) {
                $baseDir = './userFile/';
            } else {
                $baseDir = './upload/';
            }
            $uploadDir = $baseDir . $modules[0] . '/' . $modules[1] . '/' . $modules[2] . '/' . date('Ymd') . '/';
            mkdirs($uploadDir);
            if (!empty($base64)) {
                $fileInfo = $this->base64($base64, $uploadDir, $fileName);
                $uploadType = 'base64';
            } else {
                if (empty($file)) {
                    error(__('no file'));
                }
                $fileInfo = $file->getInfo();

                $fileInfo['postfix'] = substr($fileInfo['name'], strrpos($fileInfo['name'], '.') + 1);
                $fileInfo['upload_dir'] = $uploadDir;

                $splInfo = $file->move($fileInfo['upload_dir'], $fileName);

                if (is_bool($splInfo) && $splInfo == false) {
                    throw new Exception($file->getError());
                }

                $fileInfo['file_name'] = $fileName . '.' . $fileInfo['postfix'];
                $uploadType = 'form';
            }
            //用户文件权限检查
            $fileInfo['updateSize'] = $this->checkFile($fileInfo['postfix'], $fileInfo['size']);

            $imagewidth = $imageheight = 0;
            if (in_array($fileInfo['type'], ['image/gif', 'image/jpg', 'image/jpeg', 'image/bmp', 'image/png', 'image/webp'])
                || in_array($fileInfo['postfix'], ['gif', 'jpg', 'jpeg', 'bmp', 'png', 'webp'])) {

                $imgInfo = getimagesize($uploadDir . $fileInfo['file_name']);
                if (!$imgInfo || !isset($imgInfo[0]) || !isset($imgInfo[1])) {
                    $this->error(__('not valid image'));
                }
                $imagewidth = isset($imgInfo[0]) ? $imgInfo[0] : $imagewidth;
                $imageheight = isset($imgInfo[1]) ? $imgInfo[1] : $imageheight;
            }
            $key = empty($key) ?$fileName : $key;
            $url= str_replace($this->request->server('DOCUMENT_ROOT'), '', PUBLIC_PATH) .
                trim($uploadDir, '.') . $fileInfo['file_name'];
            $params = array(
                'user_id' => (int)$this->userId,
                'filesize' => $fileInfo['size'],
                'name' => $fileInfo['name'],
                'imagewidth' => $imagewidth,
                'imageheight' => $imageheight,
                'imagetype' => $fileInfo['postfix'],
                'imageframes' => 0,
                'mimetype' => $fileInfo['type'],
                'url' =>$url,
                'storage' => 'local',
                'sha1' =>'',
                'use_area' => $this->request->post('use_area', ''),
                'extparam' => json_encode($extparam, true),
                'is_used' => $is_used,
                'create_time' => time(),
                'use_module' => $modules[0],
                'use_controller' => $modules[1],
                'use_action' => $modules[2],
                'upload_type' => $uploadType,
                'key' => $key
            );
            $id = $this->model->allowField(true)->insertGetId($params);
            UserFile::change($this->userId, $fileInfo['updateSize']);
            //更新用户使用空间
            if (!$id) {
                throw new Exception(__('upload error'));
            }
            Db::commit();
            success(__('Success'), ['id' => $id, 'path' => $params['url'], 'key' => $key]);
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
    }

    public function download()
    {
        $code = $this->request->get('code');
        $file = authCode($code, 'decode');
        $file = json_decode($file, true);
        $path_parts = pathinfo($file['path']);
        if (file_exists($file['path'])) {
            $length = filesize($file['path']);
            $type = mime_content_type($file['path']);
            header("Content-Description: File Transfer");
            header('Content-type: ' . $type);
            header('Content-Length:' . $length);
            if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
                header('Content-Disposition: attachment; filename="' . rawurlencode('basename') . '"');
            } else {
                header('Content-Disposition: attachment; filename="' . $path_parts['basename'] . '"');
            }
            readfile($file['path']);
            exit;
        }
    }

    public function del()
    {
        return $this->delAttach();
    }

    /**
     * @author : 萤火虫
     * @date: 2021-3-31 下午2:59
     * @name: 删除附件
     */
    public function delAttach()
    {
        $attachId = $this->request->post('id');
        if (!$attachId) {
            $this->error('Param Error');
        }

        if (!$this->model->delFile($attachId)) {
            error(__('Error'));
        }
        success(__('Success'));
    }

    /**
     * @throws \think\exception\DbException
     * @author : yhc
     * @email : 445727994@qq.com
     * @date: 2021/8/9 9:37
     * @name: 清除不存在的文件
     */
    public function clear(){
        $files=$this->model->all();
        foreach ($files as $v){
            if(!file_exists($v['url'])){
                $this->model->where(['id'=>$v['id']])->delete();
            }
        }
    }
}
