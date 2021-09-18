<?php
/**
 * @author : yhc
 
 * @date : 2020/10/10 10:41
 * @name :
 */
namespace  app\admin\logic;
class User {
    protected  $model;
    public function __construct()
    {
        $this->model=model('hnzl\model\User');
    }
    public  function users($where,$field='id,real_name'){
        return $this->model->users($where,$field);
    }
}