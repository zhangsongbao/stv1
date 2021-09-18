<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/05 11:46
 * @name:
 */

namespace app\admin\controller;

use app\notice\controller\SwooleServer;
use app\notice\logic\NoticeDetail;
use hnzl\HnzlController;
use think\Db;
use think\facade\Log;


class Amake extends HnzlController
{
    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = '*';
    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = [];

    public function _initialize()
    {
        parent::_initialize();
        //载入model
//        $this->model=model('app\admin\model\Config');
//        //载入验证器
//        $this->validate=new \app\admin\validate\Config();
    }

    public function autoMake()
    {
        $create_module = input('module');
        if (!empty($create_module)) {
            $this->create();
        }
        return $this->fetch();
    }

    protected function cc_format($name)
    {
        $temp_array = array();
        for($i = 0;$i < strlen($name);$i++){
            $ascii_code = ord($name[$i]);
            if($ascii_code >= 65 && $ascii_code <= 90){
                if($i == 0){
                    $temp_array[] = chr($ascii_code + 32);
                }else{
                    $temp_array[] = '_' . chr($ascii_code + 32);
                }
            }else{
                $temp_array[] = $name[$i];
            }
        }
        return implode('', $temp_array);
    }

    protected function create()
    {
        //读取固定文件
        $app_path = '../application/';
        $create_name = ucfirst(input('name'));
        $create_module = input('module');
        $types = ['controller', 'model', 'validate', 'lang'];
        $replace = ['!$name!' => $create_name];
        $table = input('tablename');
        $tableName = '';
        if (!empty($table)) {
            $prefix = config()["database"]["prefix"];
            $tableName = 'protected $table = "' . $prefix . $table . '";';
        } else {
            $table = $this->cc_format(lcfirst(input('name')));
        }
        $tableMsg = $this->get_field($table);

        foreach ($types as $ks => $type) {
            if ($type == 'lang') {
                $create_path = $app_path . $create_module . '/' . $type . '/zh-cn/';
            } else {
                $create_path = $app_path . $create_module . '/' . $type . '/';
            }

            //不存在 再创建 防止覆盖 修改后的代码
            if (!file_exists($create_path . $create_name . ".php")) {
                mkdirs($create_path);
                $files = file_get_contents("../hnzl/autoCreate/" . ucfirst($type) . '.php');
                //固定替换模块  -》c or m  or v
                $files = str_replace('!$module!', $create_module, $files);
                $files = str_replace('!$table!', $create_name, $files);
                $fixed = ['!$time!' => date('Y-m-d H:i:s'), 'yhc' => "萤火虫",
                    '!$email!' => 'yhc@qq.com', '!$Auther!' => '', '!$add!' => '', '!$edit!' => '',
                    '!$tableName!' => $tableName];
                if ($type == 'validate') {
                    $rules = $msg = $validate = '';
                    if (isset($tableMsg['validate'])) {
                        foreach ($tableMsg['validate'] as $k => $v) {
                            $rules .= "'" . $k . "'  => '" . $v . "'," . PHP_EOL;
                            $validate .= "'{$k}',";
                        }
                        $replace['!$rule!'] = $rules;
                        $replace['!$msg!'] = $tableMsg['validateMsg'];
                        $replace['!$validate!'] = $validate;
                    }
                }
                if ($type == 'lang') {
                    ob_start();
                    $lang = '';
                    foreach ($tableMsg['lang'] as $k => $v) {
                        $lang .= "'" . $k . "'  => '" . $v . "'," . PHP_EOL;
                    }
                    $json = $searchJson = [];
                    $searchJson = [
                        'order' => 'desc',
                        'limit' => 15,
                        'page' => 1,
                        'sort' => 'id'
                    ];
                    $raw = [
                        "\$schema" => "http://json-schema.org/draft-04/schema#",
                        "type" => "object",
                        "properties" => [],
                        "required" => []
                    ];
                    $raw["properties"]['getData'] = [
                        "type" => 'integer',
                        "description" => '为1时 获取数据 ID',
                        "mock" => [
                            "mock" => "",
                        ],
                        "default" => '0'
                    ];
                    foreach ($tableMsg['params'] as $k => $v) {
                        if ($k == 0) {
                            $searchJson['sort'] = $v['field'];
                        }
                        $type = 'String';
                        $json[$v['field']] = $v['type'] == 'int' ? rand(1, 9) : $v['note'];
                        $searchJson['filter'][$v['field']] = $json[$v['field']];
                        $searchJson['filter'][$v['field'] . '_OP'] = $v['type'] == 'int' ? '=' : 'like';
                        if ($v['is_null'] > 0) {
                            $raw['required'][] = $v['field'];
                        }
                        switch ($v['type']) {
                            case "varchar":
                                $type = 'String';
                                if (strpos('s_material_name', $v['field']) !== false) {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'integer',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => "",
                                            "enum" => ["C30", "C35"]
                                        ],
                                        "default" => 'C30'
                                    ];
                                } else if (strpos('s_unit', $v['field']) !== false) {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'string',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => '',
                                            "enum" => ["公斤", "吨", "方"]
                                        ],
                                        "default" => '吨'
                                    ];
                                } else {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'string',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => $v['note']
                                        ],
                                        "default" => $v['note']
                                    ];
                                }
                                break;
                            case "int":
                                //$type='Integer';
                                if (strpos('s_station_code', $v['field']) !== false) {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'integer',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => "@integer(100,1099)",
                                            "enum" => [111, 222, 333, 444, 555, 666, 777, 888, 999]
                                        ],
                                        "default" => '111'
                                    ];
                                } else if (strpos('s_material_code', $v['field']) !== false) {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'integer',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => "@integer(1000,1009)",
                                        ],
                                        "default" => '1001'
                                    ];
                                } else {
                                    $raw["properties"][$v['field']] = [
                                        "type" => 'integer',
                                        "description" => $v['note'],
                                        "mock" => [
                                            "mock" => "@integer(1000,1200)",
                                        ],
                                        "default" => '1001'
                                    ];
                                }
                                break;
                            case "decimal":
                                $raw["properties"][$v['field']] = [
                                    "type" => 'number',
                                    "description" => $v['note'],
                                    "mock" => [
                                        "mock" => rand(1000, 9999) / 100,
                                    ],
                                    "default" => '10'
                                ];
                                $type = 'Float';
                                break;
                            default:

                                $raw["properties"][$v['field']] = [
                                    "type" => 'string',
                                    "description" => $v['note'],
                                    "mock" => [
                                        "mock" => $v['note'],
                                    ],
                                    "default" => $v['note']
                                ];
                                $type = 'Float';
                                break;
                        }
                        $json[] = [
                            "is_checked" => $v['is_null'],
                            "type" => "Text",
                            "key" => $v['field'],
                            'not_null' => $v['is_null'] > 0 ? $v['is_null'] : -1,
                            "value" => $v['type'] == 'int' ? 0 : $v['note'],
                            'field_type' => $type,
                            'description' => $v['note']
                        ];
                    }
                    file_put_contents($create_path . $create_name . '.json', json_encode([$json, $searchJson, $raw], true));
                    $replace['!$lang!'] = $lang;
                }
                $replace = array_merge($replace, $fixed);
                foreach ($replace as $k => $v) {
                    $files = str_replace($k, $v, $files);
                }
                $res = file_put_contents($create_path . $create_name . '.php', $files);
                if ($res) {
                    echo $create_path . $create_name . ".php " . "生成成功" . "<br>";
                } else {
                    echo $create_path . $create_name . ".php" . "生成失败" . "<br>";
                }
            } else {
                echo $create_path . $create_name . ".php" . " 已存在" . "<br>";
            }
        }
        exit;
    }


    public function text()
    {

        return $this->fetch();
    }

    public function text2()
    {
        return $this->fetch();
    }

    public function get_field($table_name = '', $field = true, $table_schema = '')
    {
        // 接收参数
        $database = config()["database"];
        $table_schema = empty($table_schema) ? $database['database'] : $table_schema;

        $table_name = $database['prefix'] . $table_name;

        // 缓存名称
        $fieldName = $field === true ? 'allField' : $field;
        $cacheKeyName = 'db_' . $table_schema . '_' . $table_name . '_' . $fieldName;
        // 处理参数
        $param = [
            $table_name,
            $table_schema,
        ];

        // 字段
        $columeName = '';
        if ($field !== true) {
            $param[] = $field;
            $columeName = "AND COLUMN_NAME = ?";
        }
        // 查询结果
        $result = Db::query("SELECT IS_NULLABLE,COLUMN_NAME as field,column_comment as comment,DATA_TYPE as type ,CHARACTER_MAXIMUM_LENGTH as length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = ? AND table_schema = ? $columeName", $param);

        if (empty($result) && $field !== true) {
            return $table_name . '表' . $field . '字段不存在';
        }
        $data = $validate = $params = [];
        $notArray = [
            'is_delete', 'id', 'create_time', 'update_time'
        ];
        $validateMsg = '';
        foreach ($result as $k => $v) {
            //IS_NULLABLE 不为空 写入
            if ($v['IS_NULLABLE'] == "NO" && !in_array($v['field'], $notArray)) {
                $validate[$v['field']] = 'require';
                $validateMsg .= "'" . $v['field'] . ".require' =>  __('Require',['s'=>__('" . $v['field'] . "')])," . PHP_EOL;
                if ($v['type'] == 'int') {
                    $validate[$v['field']] .= '|integer';
                    $validateMsg .= "'" . $v['field'] . ".integer' =>  __('Integer',['s'=>__('" . $v['field'] . "')])," . PHP_EOL;
                }
                if ($v['type'] == 'float') {
                    $validate[$v['field']] .= '|float';
                    $validateMsg .= "'" . $v['field'] . ".float' =>  __('Float',['s'=>__('" . $v['field'] . "')])," . PHP_EOL;
                }
                if ($v['length'] != null) {
                    $validate[$v['field']] .= '|length:0,' . $v['length'];
                    $validateMsg .= "'" . $v['field'] . ".length' =>  __('Length',['s'=>__('" . $v['field'] . "')])," . PHP_EOL;
                }
            }
            if ($v['comment']) {
                $comment = explode('#', $v['comment']);
                $data[$v['field']] = $comment[0];
                $params[] = ['field' => $v['field'], 'note' => $v['comment'], 'type' => $v['type'], 'is_null' => $v['IS_NULLABLE'] == "NO" ? 1 : 0];
            }
        }
        return ['lang' => $data, 'validate' => $validate, 'params' => $params, 'validateMsg' => $validateMsg];
    }

}
