<?php
/**
 * @author : yhc

 * @date : 2020/10/14 9:20
 * @name :
 */
namespace app\log\controller;
use hnzl\HnzlController;
use think\facade\Cache;

class Log extends HnzlController {
    protected $noNeedLogin = [];
    protected $noNeedRight =[];
    public function _initialize()
    {
        parent::_initialize();
        //载入model
        $this->model=model('app\log\model\Log');
    }
    public function text(){

    }
}