<?php
namespace app\purchase\logic;
use hnzl\HnzlModel;
use app\purchase\model\Purchase as Model;
/*
 * @auther 
 * @email  yhc@qq.com
 * @create_time 2021-03-05 16:59:25
 */
class Purchase {
    /**
     * @author : 萤火虫

     * @date: 2021-3-5 下午5:33
     * @name: 生成收货编码
     */
    public static function code($receiveStationCode){
        $lastCode=Model::where([
            ['receive_station_code','=',$receiveStationCode],
            ['create_time','>',strtotime(date('Y-m-d 00:00:00'))]
        ])->value("code");
        $lastCode=$lastCode??0;
        $num=sprintf ("%04d", substr($lastCode,-4)+1);
        return $receiveStationCode.date('Ymd').$num;
    }
}