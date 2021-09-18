<?php
/**
 * Created by PhpStorm.
 * @author : yhc

 * @date: 2020/11/26 10:21
 * @name:
 */
namespace  app\http;
use hnzl\model\AuthRule;
use hnzl\model\User;
use think\exception\HttpResponseException;
use think\swoole\Server;
use think\facade\Log;
use app\notice\logic\NoticeDetail;
class  SwooleServer extends Server{
    protected $port=8901;
    protected $host='0.0.0.0';
    protected $option=[
        'woker_num'=>4,
        'daemonize'=>false,
        'backlog'=>128
    ];
    protected $noticeUrl='';
    protected $serverType='socket';
    protected $redisClient;
    protected $prefix='swool_server_user_';
    public function  onHandShake($request,$response){
        $secWebSocketKey = $request->header['sec-websocket-key'];
        // websocket握手连接算法验证
        $patten = '#^[+/0-9A-Za-z]{21}[AQgw]==$#';
        if (0 === preg_match($patten, $secWebSocketKey) || 16 !== strlen(base64_decode($secWebSocketKey))) {
            $response->end();
            return false;
        }
        //echo $request->header['sec-websocket-key'];
        $key = base64_encode(
            sha1(
                $request->header['sec-websocket-key'] . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11',
                true
            )
        );
        $headers = [
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Accept' => $key,
            'Sec-WebSocket-Version' => '13',
        ];
        // WebSocket connection to 'ws://127.0.0.1:9502/'
        // failed: Error during WebSocket handshake:
        // Response must not include 'Sec-WebSocket-Protocol' header if not present in request: websocket
        if (!isset($request->header['sec-websocket-protocol'])) {
            $response->status(403);
            $response->end();
        }else{
            $headers['Sec-WebSocket-Protocol']=$request->header['sec-websocket-protocol'];
            try{
                $token=$request->header['sec-websocket-protocol'];
                if(!isset($token)){
                    throw new \Exception('server auth error');
                }
                $userMsg=auth()->user('',$token);
                //这样redis只允许一个长连接通知 改为数组 可允许多个通知
                redis($this->prefix.$userMsg['id'],$request->fd);

                redis($this->prefix.$request->fd,$userMsg);
                if($request->server['request_uri']=='/serverMsg'){
                    //检查权限
                    if(AuthRule::checkRole($userMsg['id'],'admin/config/systemConfig')){
                        $serverUser=redis($this->prefix.'serverUser');
                        $serverUser[$request->fd]=$userMsg['id'];
                        redis($this->prefix.'serverUser',$serverUser);
                    }
                };
                $headers['msg']= $this->success('success',['real_name'=>$userMsg['real_name'],'user_name'=>$userMsg['user_name'],'mobile'=>$userMsg['mobile']]);
                //$user=  redis($this->prefix.$userMsg['id']);
                $response->status(101);
                foreach ($headers as $key => $val) {
                    $response->header($key, $val);
                }
                $this->onOpen($this->swoole,$request);
                $response->end();
            }catch (HttpResponseException $e){
                $response->status(403);
                $response->end();
            }catch(\Exception $e){
                $response->status(403);
                $response->end();
            }

        }
    }
    public function  onWorkerStart($server, $worker_id){

        if ($worker_id == 0) {
            // 0 worker进程开启一个定时器去监听mysql数据变化
            //清空redis信息
//            redis($this->prefix.'*',null);
//            $server->tick(10000, [$this, 'onTick']);
//            $server->tick(1000, [$this, 'serverMsg']);
        }
    }
    public function onTick(){
        //定时提醒
        $notice= new NoticeDetail();
        $allNotice=$notice->getNoticeAll(['status'=>0],300);
        $where[]=['is_delete','=',0];
        $usersALl=User::where($where)->column('id,real_name');
        if($allNotice){
            foreach ($allNotice as $k=>$v){
                if($fd=redis($this->prefix.$v['to_user'])){
                    $this->swoole->push($fd, $this->success('success',[
                        'title'=>$v['title'],
                        'content'=>$v['content'],
                        'create_time'=>$v['create_time'],
                        'add_user'=>$usersALl[$v['add_user']]??"",
                        'up_time'=>$v['up_time']
                    ]));
                }
            }
        }
    }
    public function serverMsg(){
        //定时服务器信息
        if($serverUser=redis($this->prefix.'serverUser')){
            $info = array(
                'system' => PHP_OS,
                //'environment' => $_SERVER["SERVER_SOFTWARE"],
                //  'PHP运行方式'=>php_sapi_name(),
                'upload_limit' => ini_get('upload_max_filesize'),
                'time_limit' => ini_get('max_execution_time') . '秒',
                'time' => date("Y年n月j日 H:i:s"),
                //    'host' => $_SERVER['SERVER_NAME'],
                'disk' => $this->getDisk(),
                'sys' => $this->onlyU(PHP_OS)
            );
            foreach ($serverUser as $fd=>$userId){
                $this->swoole->push($fd, $this->success('success',$info,0,'serverMsg'));
            }
        }
    }
    public function  onOpen($server, $request){

    }

    public function  onClose($server, $fd){
        $userMsg=redis($this->prefix.$fd);
        redis($this->prefix.$fd,null);
        redis($this->prefix.$userMsg['id'],null);
        $serverUser=redis($this->prefix.'serverUser');
        if(isset($serverUser[$fd])) {
            unset($serverUser[$fd]);
            redis($this->prefix.'serverUser',$serverUser);
        }
        echo "client {$fd} closed\n";
        Log::write(json_encode($fd),$this->success('success','close'));
    }
    public function  onMessage($server, $frame){
        //echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";

    }

    public function success($msg='',$data=[],$code=0,$type='msg'){
        return $this->result($msg,$data,$code,$type);
    }
    public function error($msg='',$data=[],$code=1,$type='msg'){
        return $this->result($msg,$data,$code,$type);
    }
    public function result($msg,$data,$code,$type){
        return json_encode([
            'msg'=>$msg,
            'code'=>$code,
            'data'=>$data,
            'time'=>time(),
            'type'=>'msg'
        ],true);
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
        //磁盘内容改变较小 做缓存1min/次查询
        $cacheKey = 'hnzl_ConfigDisk';
        if(!$data=redis($cacheKey)){
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
            redis($cacheKey,$data,60);
        }
        return $data;
    }
}
