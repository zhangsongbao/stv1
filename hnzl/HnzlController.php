<?php

namespace hnzl;

use Hashids\Hashids;
use hnzl\model\AuthRule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use think\Controller;
use think\Db;
use think\Exception;
use think\exception\HttpResponseException;
use think\Lang;
use think\Loader;
use think\Request;

/**
 * @author : yhc
 
 * @date : 2020/10/9 7:57
 * @name :
 */
class HnzlController extends Controller
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
    /**
     * 模型对象
     * @var \think\Model
     */
    protected $model = null;
    protected $validate = null;
    protected $lang;
    protected $request;
    protected $userId;
    protected $exportName = '';
    public $auth='';
    protected $import = [
        'limit' => '10000',
        'type' => 'insertAll',
        'unique'=>null,
        //表格内唯一  单字段唯一  多字段不提供校验
        //其他验证 请在验证器内定义验证场景 import
        // import验证规则 禁止定义unique
        //insertAll为sql一次执行 insertAll 一般默认不进行后续数据处理  数据必须严格标准
        //  saveAll为单条添加 执行add方法逻辑 会返回insertId 可以在添加后进行其他数据处理  不建议使用saveAll
    ];

    public function __construct(Request $request, Lang $lang)
    {

        $this->request = $request;
        $this->lang = $lang;
        $this->_initialize();
    }

    public function _initialize()
    {
        $this->loadLang($this->request->controller());
        //导入基础中文语言包
        $this->lang->load(dirname(__DIR__) . '/hnzl/lang/zh-cn.php');
        //权限判断
        $this->checkAuth();
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadLang($name)
    {

        $this->lang->load(dirname(__DIR__) . '/application/' . $this->request->module() . '/lang/' .
            $this->request->langset() . '/' .
            str_replace('.', '/', ucfirst($name)) . '.php');

    }

    /**
     * @param string $type false 不记录日志
     * @param bool $with
     * @author : yhc
     * @email : 445727994@qq.com
     * @date: 2021/7/31 16:13
     * @name:
     */
    public function index($type = '4', $with = true)
    {
        if ($this->request->isPost()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $model = $this->model;

            if (method_exists($this, '_before_index')) {
                $this->_before_index($where, $model, $with);
            }
            $list = $model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $total = $model
                ->where($where)
                ->count();

            $result = array("total" => $total, "rows" => $list);
            if (method_exists($this, '_end_index')) {
                $this->_end_index($result);
            }
            if ($type != false) {
                logs()->log([], $type);
            }
            success(__("Success"), $result);
        }
    }

    /**
     * @return mixed
     * @author : yhc
     
     * @date: 2020/10/14 下午2:33
     * @name: 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $insert = $this->request->post();
            $insert['create_time'] = $insert['update_time'] = time();
            Db::startTrans();
            try {
                if ($this->validate) {
                    if (!$this->validate->scene('add')->check($insert)) {
                        error($this->validate->getError(), '', 2);
                    }
                }

                //检验添加站点是否在权限内
                if (isset($this->model->station_code) && !empty($insert[$this->model->station_code])) {

                    if (!in_array($insert[$this->model->station_code], auth()->stations())) {
                        error(__('Station Error'));
                    }
                }
                if (method_exists($this, '_before_add')) {
                    $this->_before_add($insert);
                }
                $allowField = $this->model->allowFieldAdd ?? $this->model->allowField;
                if (!$this->model->allowField($allowField)->save($insert)) {
                    throw  new Exception(__('Add Error'));
                }
                if (method_exists($this, '_end_add')) {
                    $insert['id'] = $this->model->getLastInsID();
                    $this->_end_add($insert);
                }
                logs()->log($insert, 1);
                Db::commit();
            } catch (HttpResponseException $e) {
                Db::rollback();
                throw new HttpResponseException($e->getResponse());
            } catch (Exception $e) {
                Db::rollback();
                error($e->getMessage());
            }
            success(__('Add Success'),$insert);
        }

    }


    /**
     * @author : yhc
     
     * @date: 2020/10/19 上午9:31
     * @name:单条编辑
     */
    public function edit()
    {
        $pk = $this->model->getPk();
        $cacheKey = 'hnzl_' . $this->request->controller() . '_edit_'.$this->request->post($pk);
        if ($this->request->post('getData') == 1) {
            $oldData = $this->view(1);
            redis($cacheKey, $oldData, 600);
            //缓存记录N分钟 方便日志记录
            success(__('Success'), $oldData);
        }
        $updateArr = $this->request->post();
        $where[$pk] = $this->request->post($pk);

        Db::startTrans();
        try {
            if (!$oldData = redis($cacheKey)) {
                $oldData = $this->view(1);
            }
            if ($this->validate) {
                if (!$this->validate->scene('edit')->check($updateArr)) {
                    error($this->validate->getError(), '', 2);
                }
            }
            if (method_exists($this, '_before_edit')) {
                $this->_before_edit($updateArr, $oldData);
            }
            if (isset($this->model->station_code)) {
                //检验旧数据站点是否在权限内
                if (isset($oldData[$this->model->station_code])) {
                    if (isset($oldData[$this->model->station_code])) {
                        if (!in_array($oldData[$this->model->station_code], auth()->stations())) {
                            error(__('Station Error'));
                        }
                    }
                }
                //验证更新站点是否在权限内
                if (isset($updateArr[$this->model->station_code])) {
                    if (!in_array($updateArr[$this->model->station_code], auth()->stations())) {
                        error(__('Station Error'));
                    }
                }
            }

            $allowField = $this->model->allowFieldEdit ?? $this->model->allowField;
            $this->model->allowField($allowField)->save($updateArr, $where);
            logs()->log(['oldData' => $oldData, 'newData' => $this->view(1)], 3);
            if (method_exists($this, '_end_edit')) {
                $this->_end_edit($updateArr, $oldData);
            }
            redis($cacheKey, null);
            Db::commit();
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
        success(__('Edit Success'),$updateArr);
    }

    /**
     * @author : yhc
     
     * @date: 2020/10/29 09:03
     * @name: 单条获取
     */
    public function view($getData = 0)
    {
        $pk = $this->model->getPk();
        $where[] = [$pk, '=', $this->request->post($pk)];
        $model = $this->model;
        if (isset($this->model->station_code)) {
            $stationWhere = $this->stationWhere($this->model->station_code);
            if (count($stationWhere) > 0) {
                $where[] = $stationWhere;
            }
        }
        if (method_exists($this, '_before_view')) {
            $this->_before_view($where, $model);
        }

        $data = $model->where($where)->find();
        if (!$data) {
            error(__('View Error'));
        }
        if (method_exists($this, '_end_view')) {
            $this->_end_view($data);
        }
        if ($getData == 1) {
            return $data;
        }

        logs()->log(['viewData' => $data], 4);
        success(__('Success'), $data);
    }

    /**
     * @author : yhc
     
     * @date: 2020/10/29 09:03
     * @name: 单条删除
     */
    public function del()
    {
        $pk = $this->model->getPk();
        $delete = $this->request->post($pk);
        $where[] = [$pk, '=', $delete];
        if (isset($this->model->station_code)) {
            $stationWhere = $this->stationWhere($this->model->station_code);
            if (count($stationWhere) > 0) {
                $where[] = $stationWhere;
            }
        }
        Db::startTrans();
        try {
            $deleteData = $this->model->where($where)->find();
            if (!$deleteData) {
                throw new Exception(__("Not Exists"));
            }
            if (method_exists($this, '_before_del')) {
                $this->_before_del($deleteData);
            }
            if ($this->model->softDelete) {
                if (!$this->model->where($where)->update([$this->model->softDelete => 1])) {
                    throw new Exception(__("Del Error"));
                }
            } else {
                if (!$this->model->where($where)->delete()) {
                    throw new Exception(__("Del Error"));
                }
            }
            if (method_exists($this, '_end_del')) {
                $this->_end_del($deleteData);
            }
            logs()->log(['deleteData' => $deleteData], 2);
            Db::commit();
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
        success(__('Del Success'));

    }

    /**
     * @author : yhc
     
     * @date: 2020/10/29 09:02
     * @name: 批量编辑
     */
    protected function editAll($updateArr = [],$field=true, $logId = 33)
    {
        $updateArr = count($updateArr) > 0 ? $updateArr : $this->request->post('update');
        list($where, $sort, $order, $offset, $limit) = $this->buildparams(true);

        $model = $this->model;
        if (method_exists($this, '_before_index')) {
            $this->_before_index($where, $model, false);
        }
        Db::startTrans();
        try {
            $updateArr['update_time'] = time();
            $this->model->allowField($field)->isUpdate(true)->save($updateArr, $where);
            logs()->log(['oldData' => [], 'update' => $updateArr, 'where' => $where], $logId);
            Db::commit();
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
        success(__('Edit Success'));
    }

    /**
     * @throws Exception
     * @author : yhc
     
     * @date: 2020/10/29 09:02
     * @name: 批量删除
     */
    public function delAll()
    {
        list($where, $sort, $order, $offset, $limit) = $this->buildparams(true);
        $model = $this->model;
        if (method_exists($this, '_before_index')) {
            $this->_before_index($where, $model, false);
        }
        $list = $this->model->where($where);
        if (method_exists($this, '_before_delAll')) {
            $this->_before_delAll($list);
        }
        if ($this->model->softDelete) {
            if ($list->update([$this->model->softDelete => 1])) {
                logs()->log(['deleteData' => $this->model->where($where)->select(), 'softDelete' => 1, 'where' => $where], 22);
                success(__('Del Success'));
            }
        } else {
            if ($list->delete()) {
                logs()->log(['deleteData' => $this->model->where($where)->select(), 'where' => $where], 22);
                success(__('Del Success'));
            }
        }
        error(__("Del Error"));
    }


    /**
     * @author : 萤火虫
     
     * @date: 2021-3-26 上午9:30
     * @name: 后台导入 未完成
     */
    public function import()
    {
        set_time_limit(0);
        $path = $this->request->path('path');
        $Lang = include dirname(__DIR__) . '/application/' . $this->request->module() . '/lang/' .
            $this->request->langset() . '/' .
            str_replace('.', '/', ucfirst($this->request->controller())) . '.php';
        $arrayField = array_flip($Lang);
        $paths = explode('/', $path);
        $user_id = $paths[2];
        if ($user_id != $this->userId) {
            error(__("Auth Error"));
        }
        /**  1.检测文件类型  **/
        $fileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($path);
        /**  2.根据类型创建合适的读取器对象  **/
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($fileType);
        $reader->setReadDataOnly(true);
        $PHPExcel = $reader->load($path); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestDataRow(); // 取得总行数
        $highestColumm = $sheet->getHighestDataColumn(); // 取得总列数
        $data = [];
        $titleArray = [];
        $row = 1;

        for ($column = 'A'; $column <= $highestColumm; $column++) //列数是以A列开始
        {
            $tmp = $sheet->getCell($column . $row)->getValue();
            if (empty($tmp)) {
                $column--;
                $highestColumm = $column;
                break;
            }
            if (!isset($arrayField[$tmp])) {
                error($tmp . __('Not Exists'));
            }
            $titleArray[$column] = $arrayField[$tmp];
        }
        for ($row = 2; $row <= $highestRow; $row++) //行号从1开始
        {
            $tmp = [];
            for ($column = 'A'; $column <= $highestColumm; $column++) //列数是以A列开始
            {
                $tmp[$titleArray[$column]] = $sheet->getCell($column . $row)->getValue();
            }
            $data[] = $tmp;
        }
        $this->import_data($data);
    }

    /**
     * @throws Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @author : 萤火虫
     
     * @date: 2021-3-26 上午9:30
     * @name: 后台导出  未完成  -暂不处理
     */
    public function export()
    {
        set_time_limit(0);
        list($where, $sort, $order, $offset, $limit) = $this->buildparams();
        $model = $this->model;
        if (method_exists($this, '_before_index')) {
            $this->_before_index($where, $model);
        }
        $first = $model
            ->where($where)
            ->order($sort, $order)
            ->limit($offset, $limit)
            ->find();
        $total = $model
            ->where($where)
            ->count();

        if (!$first) {
            error('View Error');
        }
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $first = $first->toArray();
            $column = 'A';
            foreach ($first as $key => $value) {
                // 单元格内容写入
                $sheet->setCellValue($column . '1', __($key));
                $sheet->getStyle($column . '1')->getFont()->setBold(true);
                $column++;
            }
            $row = 2; // 从第二行开始
            $pageSize = 500;
            $pages = ceil($total / $pageSize);
            for ($i = 0; $i < $pages; $i++) {
                $list = $model
                    ->where($where)
                    ->order($sort, $order)
                    ->limit($i * $pageSize, $pageSize)
                    ->select();
                if ($list) {
                    $list = $list->toArray();

                    foreach ($list as $item) {
                        $column = 'A';
                        foreach ($item as $value) {
                            // 单元格内容写入
                            $sheet->setCellValue($column . $row, (string)$value);
                            $column++;
                        }
                        $row++;
                    }
                }

            }
            $savePath = './export/' . hashids($this->userId) . '/' . date('Y-m-d') . '/';
            //$fileName=md5(uniqid(md5(microtime(true)),true)).'xlsx';
            $fileName = $this->exportName . '_' . date('Y-m-d') . '.xlsx';
            /**  1.检测文件类型  **/
            mkdirs($savePath);
            $writer = new Xlsx($spreadsheet);
            $writer->save($savePath . $fileName);
            if (!file_exists($savePath . $fileName)) {
                throw new Exception(__('Export Error'));
            }
            $json = ['path' => $savePath . $fileName, 'time' => time()];
            $code = authCode(json_encode($json));
            success(__('Success'), ['url' => url('admin/attachment/download') . '?code=' . urlencode($code)]);
            //返回加密链接  输出供前端下载
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }

    }
    /**
     * @author : 萤火虫
     
     * @date: 2021-3-26 上午9:48
     * @name: 前端导入
     */
    public function importData()
    {
        set_time_limit(0);
        $data = $this->request->post('data/a');
        if (count($data) > $this->import['limit']) {
            error(__("limit", ['s' => $this->import['limit']]));
        }
        $errMsg = [];
        $time = time();

        if (method_exists($this, '_import_data_check')) {
            $this->_import_data_check($data);
        }else{
            $uniqueField=$this->import['unique'];
            if ($this->validate) {
                //单独验证规则
                $uniqueStr='';
                if($this->validate->hasScene('import')){
                    if(isset($uniqueField)){
                        foreach ($data as $k => &$v) {
                            if (!$this->validate->batch()->scene('import')->check($v)) {
                                $errMsg[$k] = ['line' => $k, 'msg' => $this->validate->getError()];
                            }
                            $v['update_time'] = '-'.$this->userId;
                            $v['create_time'] = $time;
                            if(isset($uniqueData[$v[$this->import['unique']]])){
                                if(isset($errMsg[$k])){
                                    $errMsg[$k]['msg'][$uniqueField] =__('Repeat',['s'=>$k+1,
                                        'd'=>$uniqueData[$v[$uniqueField]]+1]);
                                }else{
                                    $errMsg[$k] = ['line' => $k, 'msg' =>
                                        [ $uniqueField=>__('Repeat',['s'=>$k+1,
                                            'd'=>$uniqueData[$v[$uniqueField]]+1])]];
                                }
                            }else{
                                $uniqueData[$v[$uniqueField]]=$k;
                            }
                            $uniqueStr.=','.$v[$uniqueField];
                        }

                    }else{
                        foreach ($data as $k => &$v) {
                            if (!$this->validate->batch()->scene('import')->check($v)) {
                                $errMsg[$k] = ['line' => $k, 'msg' => $this->validate->getError()];
                            }
                            $v['update_time'] = '-'.$this->userId;
                            $v['create_time'] = $time;
                        }
                    }
                }else{
                    if(isset($uniqueField)){
                        foreach ($data as $k => &$v) {
                            $v['update_time'] = '-'.$this->userId;
                            $v['create_time'] = $time;
                            if(isset($uniqueData[$v[$uniqueField]])){
                                if(isset($errMsg[$k])){
                                    $errMsg[$k]['msg'][$uniqueField] =__('Repeat',['s'=>$k+1,
                                        'd'=>$uniqueData[$v[$uniqueField]]+1]);
                                }else{
                                    $errMsg[$k] = ['line' => $k, 'msg' =>
                                        [ $uniqueField=>__('Repeat',['s'=>$k+1,
                                            'd'=>$uniqueData[$v[$uniqueField]]+1])]];
                                }
                            }else{
                                $uniqueData[$v[$uniqueField]]=$k;
                            }
                            $uniqueStr.=','.$v[$uniqueField];
                        }

                    }
                }
            }
        }

        if($uniqueStr!=''){
            $where[]=[$uniqueField,'in',trim($uniqueStr,',')];
            if (isset($this->model->softDelete)) {
                if ($this->model->softDelete == true) {
                    $where[] = ['is_delete', '=', '0'];
                }

            }
            $uniqueMysqlData=$this->model->field($uniqueField)->where($where)->select();

            if($uniqueMysqlData){
                //数据库重复
                foreach ($uniqueMysqlData as $k=>$v){
                    if(isset($errMsg[$k])){
                        $errMsg[$uniqueData[$v[$uniqueField]]]['msg'][$uniqueField] =__('Exist',['s'=>$k+1]);
                    }else{
                        $errMsg[$uniqueData[$v[$uniqueField]]]= ['line' => $k, 'msg' =>
                            [ $uniqueField=>__('Exist',['s'=>$v[$uniqueField]])]];
                    }
                }
            }
        }

        if (count($errMsg) > 0) {
            error(__("Error"), $errMsg);
        }


        $functionName = 'type' . $this->import['type'];
        $this->$functionName($data,$time);
    }

    protected function typeInsertAll(&$data,&$time)
    {
        Db::startTrans();
        try {
            if (method_exists($this, '_before_import')) {
                $this->_before_import($data);
            }
            //添加时  去除多余字段
            $filed=$this->model->getQuery()->getTableInfo('', 'fields');

            foreach ($filed  as $k=>$v){
                $filed[$v]=1;
                unset($filed[$k]);
            }
            if (method_exists($this, '_end_import')) {
                $insertData=$data;
            }else{
                $insertData=&$data;
            }

            array_walk($insertData,function (&$value,$key) use (&$filed){
                foreach ($value as $k=>$v){
                    if(!isset($filed[$k])){
                        unset($value[$k]);
                    }
                }
            });

            $res=$this->model->insertAll($insertData);
            if(!$res){
                throw new \Exception(__('Error'));
            }
            unset($insertData);
            //根据更新时间-1 查找所有insertId及数据
            $insertData=$this->model->where(['update_time'=> '-'.$this->userId])->select()->toArray();
            //恢复为正常时间
            $this->model->where(['update_time'=> '-'.$this->userId])->update(['update_time'=>$time]);
            if (method_exists($this, '_end_import')) {
                //组合insert之前数据
                foreach ($insertData as $k=>&$v){
                    $v=array_merge($v,$data[$k]);
                }
                unset($data);
                $this->_end_import($insertData);
            }

            logs()->log(['import' => logs()->fileLog($insertData,time()), 'import'], '5');
            Db::commit();
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
        success(__("Success"));
    }

    protected function typeSaveAll(&$data,&$time)
    {
        Db::startTrans();
        try {
            if (method_exists($this, '_before_import')) {
                $this->_before_import($data);
            }
            $allowField = isset($this->model->allowFieldAdd) ? $this->model->allowFieldAdd : $this->model->allowField;

            foreach ($data as &$insert) {
                if (method_exists($this, '_before_add')) {
                    $this->_before_add($insert);
                }

                if (!$res = $this->model->allowField($allowField)->isUpdate(false)->data($insert, true)->save()) {
                    throw  new Exception(__('Add Error'));
                }
                if (method_exists($this, '_end_add')) {
                    $insert['id'] = $this->model->getLastInsID();
                    $this->_end_add($insert);
                }
            }
            logs()->log(['import' => logs()->fileLog($data,time()), 'import'], '5');
            Db::commit();
        } catch (HttpResponseException $e) {
            Db::rollback();
            throw new HttpResponseException($e->getResponse());
        } catch (Exception $e) {
            Db::rollback();
            error($e->getMessage());
        }
        success(__("Success"));
    }

    /**
     * @author : 萤火虫
     
     * @date: 2021-3-26 上午9:52
     * @name: 前段导出
     */
    public function exportData()
    {
        return $this->index(6);
    }

    /**
     * @param bool $check check 为true 则where不能为空
     * @return array
     * @author : yhc
     
     * @date: 2020/10/29 09:01
     * @name: 组建where条件
     */
    protected function buildParams($check = false)
    {
        $filter = $this->request->post("filter/a", '');
        $sort = $this->request->post("sort", !empty($this->model) && $this->model->getPk() ? $this->model->getPk() : 'id');
        $order = $this->request->post("order", "DESC");
        $page = $this->request->post("page/d", 1);
        $limit = $this->request->post("limit/d", 15);
        $offset = ($page - 1) * $limit;
        if ($limit == '-1' || $limit > 10000) {
            $limit = 10000;
            $offset = 0;
        }

        $filed=$this->model->getTableFields();
        if(is_array($sort)) {
            foreach ($sort as $k=>$v){
                if(!in_array($v,$filed)){
                    unset($sort[$k]);
                }
            }
        }else{
            $sort=explode(',',$sort);
            foreach ($sort as $k=>$v){
                if(!in_array($v,$filed)){
                    unset($sort[$k]);
                }
            }
            $sort=implode(',',$sort);
        }

        $filter = $filter ? $filter : [];
        $where = [];
        $tableName = '';
        foreach ($filter as $k => $v) {
            if (strpos($k, '_OP')) {
                continue;
            }
            $syms = $filter[$k . '_OP'] ?? '=';
            //注销op 取消循环
            unset($filter[$k . '_OP']);
            if (!is_array($syms)) {
                $syms = [$syms];
            }
            foreach ($syms as $ks => $sym) {
                if (stripos($k, ".") === false) {
                    $k = $tableName . $k;
                }
                $useV = is_array($v) ? $v[$ks] : $v;
                $searchField = $this->model->searchField ?? [];

                if (count($searchField) > 0) {

                    if (!in_array($k, $searchField)) {
                        continue;
                    }

                }
                $sym = strtoupper($sym);
                if ($useV === '' && $sym = 'EMPTY') {
                    continue;
                }
                switch ($sym) {
                    case 'EMPTY':
                        $where[] = [$k, '=', ''];
                        break;
                    case '=':
                    case '<>':
                        $where[] = [$k, $sym, (string)$useV];
                        break;
                    case 'LIKE':
                    case 'NOT LIKE':
                    case 'LIKE %...%':
                    case 'NOT LIKE %...%':
                        $where[] = [$k, trim(str_replace('%...%', '', $sym)), "%{$useV}%"];
                        break;
                    case '>':
                    case '>=':
                    case '<':
                    case '<=':
                        $where[] = [$k, $sym, intval($useV)];
                        break;
                    case 'FINDIN':
                    case 'FINDINSET':
                    case 'FIND_IN_SET':
                        $where[] = "FIND_IN_SET('{$useV}', " . $k . ")";
                        break;
                    case 'IN':
                    case 'IN(...)':
                    case 'NOT IN':
                    case 'NOT IN(...)':
                        $where[] = [$k, str_replace('(...)', '', $sym), is_array($useV) ? $useV : explode(',', $useV)];
                        break;
                    case 'LIKE':
                    case 'LIKE %...%':
                        $where[] = [$k, 'LIKE', "%{$useV}%"];
                        break;
                    case 'NULL':
                    case 'IS NULL':
                    case 'NOT NULL':
                    case 'IS NOT NULL':
                        $where[] = [$k, strtolower(str_replace('IS ', '', $sym))];
                        break;
                    default:
                        break;
                }
            }
        }
        if (count($where) < 1 && $check === true) {
            error(__('Params Error'));
        }
        if (isset($this->model->softDelete)) {
            if ($this->model->softDelete == true) {
                $where[] = [$this->model->getTable() . '.is_delete', '=', '0'];
            }
        }

        if (isset($this->model->station_code)) {
            //存在提交站点 验证是否在权限内
            if (isset($filter[$this->model->station_code]) && !empty($filter[$this->model->station_code])) {
                if (!in_array($filter[$this->model->station_code], auth()->stations())) {
                    error(__('Station Error'));
                }
            } else {
                $stationWhere = $this->stationWhere($this->model->station_code);
                if (count($stationWhere) > 0) {
                    $where[] = $stationWhere;
                }
            }
        }
//        $where = function ($query) use ($where) {
//            foreach ($where as $k => $v) {
//                if (is_array($v)) {
//                    call_user_func_array([$query, 'where'], $v);
//                } else {
//                    $query->where($v);
//                }
//            }
//        };

        return [$where, $sort, $order, $offset, $limit];
    }

    /**
     * @author : yhc
     
     * @date: 2020/10/29 09:35
     * @name: 检查用户权限
     */
    protected function checkAuth()
    {

        $action = $this->request->action(true);
        if (is_string($this->noNeedLogin)) {
            if ($this->noNeedLogin !== '*') {
                if ($action != $this->noNeedLogin) {
                    //判断登录
                    $this->userId = auth($this->auth)->user("id");
                }
            }
        } else {

            if (!in_array($action, $this->noNeedLogin)) {
                //判断登录
                $this->userId = auth($this->auth)->user("id");
            }
        }
        if ($this->userId) {
            $this->checkRight($action);
        }
    }

    /**
     * @param $action
     * @return bool
     * @author : yhc
     
     * @date: 2020/10/29 09:34
     * @name:检查用户操作权限
     */
    protected function checkRight($action)
    {
        if (is_string($this->noNeedRight)) {
            if ($this->noNeedLogin !== '*') {
                if ($action != $this->noNeedRight) {
                    if ($action != $this->noNeedRight && AuthRule::checkRole($this->userId) === false) {
                        error(__('Auth Error'));
                    }
                }
            }
        } else {
            if (!in_array($action, $this->noNeedRight)) {
                if (!in_array($action, $this->noNeedRight) && AuthRule::checkRole($this->userId) === false) {
                    error(__('Auth Error'));
                }
            }
        }

    }

    protected function stationWhere($field_name = 'station_code', $prefix = null)
    {
        $prefix = $prefix ? $prefix . '.' : '';
        $station_codes = auth()->stations();
        $count = count($station_codes);
        $where = [];
        if ($count == 1) {
            $where = [$prefix . $field_name, '=', $station_codes[0]];
        } else if ($count < count(auth()->stationAll())) {
            $where = [$prefix . $field_name, 'in', $station_codes];
        }
        return $where;
    }
}