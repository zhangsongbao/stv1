<?php
/*
 * @auther 萤火虫
 * @email  yhc@qq.com
 * @create_time 2020-11-07 08:48:04
 */

namespace app\admin\controller;
use hnzl\HnzlController;
class SystemLogin extends HnzlController
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
    public $field = 'id,name,value,type,create_time,update_time,note';
    protected $type = 'login';

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
        $where[] = ['type', '=', $this->type];
        $where[] = ['is_delete', '=', 0];
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
        $insert['type'] = $this->type;
    }

    /**
     * @param $insert
     * @author : yhc

     * @date: 2020/11/18 09:11
     * @name: 添加后置  用户处理编辑后连带处理
     */
    public function _end_add(&$insert)
    {

        redis('admin_config' . $this->type . '*', null);
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
        redis('admin_config' .  $this->type . '*', null);

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
        $where[] = ['type', '=', $this->type];
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
        $login_default=$this->model->config('login_default');
        if($deleteData['value']==array_values($login_default)[0]){
            $login_default=$this->model->where(['type'=>['=','login']])->value('value');
            if(!$login_default){
                error(__('no more'));
            }
            $this->model->where(['type'=>['=','login_default']])->update(['value'=>$login_default]);
            redis('admin_configlogin_default*', null);
        }
    }


}
