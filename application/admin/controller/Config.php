<?php
/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-07 08:48:04
 */

namespace app\admin\controller;
use hnzl\HnzlController;
class Config extends HnzlController
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
    protected $noNeedRight = ['getConfig'];
    public $field = 'id,name,value,type,create_time,update_time,note';
    protected $type = ['login_default', 'title', 'logo'];

    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model = model('app\admin\model\Config');
        //载入验证器
        $this->validate = new \app\admin\validate\Config();
    }

    /**
     * @param $where
     * @param $model
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 查询前置   用于处理查询条件 添加联查等
     */
    public function _before_index(&$where, &$model, $with = true)
    {
        //config根据type类型判断权限
        $op = is_array($this->type) ? "in" : '=';
        $where[] = ['type', $op, $this->type];
        $model = $model->field($this->field);
    }

    /**
     * @param  $result  array  array("total" => $total, "rows" => $list);
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 查询后置   用于处理查询结果
     */
    public function _end_index(&$result)
    {
        foreach ($result['rows'] as $k => $v) {
            if ($v['type'] == 'logo') {
                $result['rows'][$k]['value'] = \app\admin\model\Attachment::file($v['value']);
            }
        }

    }

    /**
     * @param $insert
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 添加前置   用于处理编辑前数据
     */
    public function _before_add(&$insert)
    {
        $insert['is_delete'] = 0;
        if (is_array($this->type)) {
            if (!isset($insert['type'])) {
                error(__('Require', ['s' => __('type')]));
            }
            if (!in_array($insert['type'], $this->type)) {
                error(__('Require', ['s' => __('type')]));
            }
        } else {
            $insert['type'] = $this->type;
        }
    }

    /**
     * @param $insert
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert)
    {
        $type = is_array($this->type) ? implode('-', $this->type) : $this->type;
        redis('admin_config' . $type . '*', null);
    }

    /**
     * @param $updateArr
     * @param $oldData
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 编辑前置   用于处理编辑前数据
     */
    public function _before_edit(&$updateArr, $oldData)
    {
        $updateArr['is_delete'] = 0;
        //禁止修改type类型
        $updateArr['type']=$oldData['type'];
    }

    /**
     * @param $updateArr1
     * @param $oldData
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 编辑后置  用户处理编辑后连带处理
     */
    public function _end_edit(&$updateArr, $oldData)
    {
        $type = is_array($this->type) ? implode('-', $this->type) : $this->type;
        redis('admin_config' . $type . '*', null);

    }

    /**
     * @param $where
     * @param $model
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 查看前置（单条数据查看）
     */
    public function _before_view(&$where, &$model)
    {
        //config根据type类型判断权限
        $op = is_array($this->type) ? "in" : '=';
        $where[] = ['type', $op, $this->type];
        $model = $model->field($this->field);
    }

    /**
     * @param $data
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 查看后置（单条数据查看）
     */
    public function _end_view($data)
    {
        $data = $data->toArray();
        switch ($data['type']) {
            case "login_default":
                $loginConfig = $this->model->config('login', 'id,name,value,note');
                foreach ($loginConfig as $k=>$v){
                    $loginConfig[$k]='';
                }
                $data['datas'] = $loginConfig;
                break;
            case "logo":
                $data['img']= \app\admin\model\Attachment::file($data['value']);
                break;
        }
    }

    /**
     * @param $deleteData
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 删除前置
     */
    public function _before_del(&$deleteData)
    {

    }

    /**
     * @param $deleteData
     * @author : 萤火虫

     * @date: 2020-11-07 08:48:04
     * @name: 删除后置
     */
    public function _end_del(&$deleteData)
    {

    }

    /**
     * @author : yhc

     * @date: 2020/11/09 10:23
     * @name: 获取key=>value配置接口 $configName 可多传数组、或，隔开string
     */
    public function getConfig()
    {
        $configName = $this->request->post('config');
        if (!is_array($configName)) {
            $configName = explode(',', $configName);
        }
        foreach ($configName as $k => $v) {
            $configs[$v] = $this->model->config($v);
        }
        success(__('Success'), $configs);
    }

    /**
     * @author : yhc

     * @date: 2020/12/21 11:29
     * @name: 综合所有select做接口  暂时不做    看具体后期需求
     */
    public function getSelect(){
        $configName = $this->request->post('config');
        if (!is_array($configName)) {
            $configName = explode(',', $configName);
        }
        foreach ($configName as $k => $v) {
            $configs[$v] = $this->model->config($v);
        }
        success(__('Success'), $configs);
    }
        /**
     * @author : yhc

     * @date: 2020/11/09 15:49
     * @name:清空redis缓存
     */
    public function clearCache()
    {
        redis('*', null);
        success(__('Clear'));
    }

    public function systemConfig()
    {
        $info = array(
            'system' => PHP_OS,
            'environment' => $_SERVER["SERVER_SOFTWARE"],
            //  'PHP运行方式'=>php_sapi_name(),
            'upload_limit' => ini_get('upload_max_filesize'),
            'time_limit' => ini_get('max_execution_time') . '秒',
            'time' => date("Y年n月j日 H:i:s"),
            'host' => $_SERVER['SERVER_NAME'],
            'disk' => $this->getDisk(),
            'sys' => $this->onlyU(PHP_OS)
        );
        success($info);
    }

    protected function getRam()
    {
        $mem = 0;
        $fh = fopen('/proc/meminfo', 'r');
        while ($line = fgets($fh)) {
            $pieces = array();
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $mem = $pieces[1];
                break;
            }
        }
        fclose($fh);
        return round($mem / 1000 / 1000) . 'g';
    }

    protected function OnlyU($system)
    {
        if ($system == 'Linux') {
            $cpu = $this->getCpuBase();
            //获取CPU使用率以及内存使用率
            $fp = popen('top -b -n 1|head -n 5', "r");
            $rs = fread($fp, 1024);
            $sys_info = explode("\n", $rs);
            $cpu_info = explode(",", $sys_info[2]);
            //  $cpu_usage = trim(trim($cpu_info[0], '%Cpu(s): '), 'us')+; //百分比
            $cpu['free'] = (float)trim($cpu_info[3], 'id');
            $cpu['used'] = round(100 - (float)trim($cpu_info[3], 'id'), 2);
            $mem_info = explode(",", $sys_info[3]); //内存占有量 数组
            $mem = [
                'total' => trim(trim($mem_info[0], 'KiB Mem : '), ' total'),
                'used' => trim(trim($mem_info[2], 'used'))
            ];
            $cpu['core'] = count($cpu['core']);
            return ['cpu' => $cpu, 'mem' => $mem];
        }
        return false;
    }

    protected function getCpuBase()
    {
        $cacheKey = 'hnzl_ConfigCpu';
        if (!$cpu = redis($cacheKey)) {
            $cpu['num'] = 0;
            $cpu['core'] = [];
            $fh = fopen('/proc/cpuinfo', 'r');
            while ($line = fgets($fh)) {
                $pieces = array();
                if (preg_match('/^(model name[\s\S]*:)([\s\S]*)$/', $line, $pieces)) {
                    if (isset($pieces[2])) {
                        $cpu['info'] = trim($pieces[2]);
                    }
                    // break;
                }
                if (preg_match('/^(core id[\s\S]*:)([\s\S]*)$/', $line, $pieces)) {
                    if (isset($pieces[2])) {
                        $cpu['core'][$pieces[2]] = $pieces[2];
                    }
                    $cpu['num']++;
                    // break;
                }
            }
            fclose($fh);
            redis($cacheKey, $cpu);
        }
        return $cpu;
    }

    public function getDisk()
    {
        //获取磁盘占用率
        $fp = popen('df -lh | grep -E "^(/)"', "r");
        $rs = fread($fp, 1024);
        pclose($fp);
        $rs = preg_replace('/\s{2,}/', ' ', $rs);  //把多个空格换成 “_”
        $all = explode("\n", $rs);
        $data = [];
        foreach ($all as $k => $v) {
            $hd = explode(" ", $v);
            $data[] = $hd;
        }
        return $data;
    }
}
