<?php
/**
 * @author : yhc

 * @date : 2020/10/9 8:32
 * @name :
 */

namespace hnzl;
use Firebase\JWT\JWT;
use hnzl\model\AuthGroup;
use hnzl\model\AuthRule;
use think\Config;
class HnzlAuth
{
    protected $tokenKey = 'token';
    protected $encrypt='td_user';
    /**
     * @var object 对象实例
     */
    protected static $instance;
    protected $userInfo = null;

    /**
     * 获取当前容器的实例（单例）
     * @access public
     * @return static
     */
    public static function getInstance($tokenKey = '')
    {
        if (null === self::$instance) {
            self::$instance = new self($tokenKey);
        }

        return self::$instance;
    }

    public function __construct($tokenKey = '')
    {
        if (!empty($tokenKey)) {
            $this->tokenKey = $tokenKey;
        }
    }

    /**
     * @param $user 用户信息
     * @return string
     * @author : yhc

     * @date: 2020/10/9 8:39
     * @name:生成登录token
     */
    public function getToken($user)
    {
        $token = [
            "iss" => 'hnzl', //签发者 可以为空
            "aud" => "", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time(), //在什么时候jwt开始生效 （这里表示生成100秒后才生效）
            "exp" => time() + \config('jwt.expire'), //token 过期时间
            "user_info" => $user //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        $jwt = JWT::encode($token, $this->encrypt, "HS256"); //根据参数生成了 token
        return $jwt;
    }

    /**
     * @param $user 用户信息
     * @return string
     * @author : yhc

     * @date: 2020/10/9 8:39
     * @name:生成登录token
     */
    public function refreshToken($user)
    {
        $token = [
            "iss" => 'refresh-hnzl', //签发者 可以为空
            "aud" => "", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time(), //在什么时候jwt开始生效 （这里表示生成100秒后才生效）
            "exp" => time() +  \config('jwt.refresh'), //token 过期时间
            "user_info" => $user //记录的user的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        $jwt = JWT::encode($token, 'td-user-'.$this->encrypt, "HS256"); //根据参数生成了 token
        return $jwt;
    }

    public function auth()
    {

    }

    /**
     * @param string $key
     * @return array|mixed
     * @author : yhc

     * @date: 2020/10/13 1:05
     * @name:获取用户信息
     */
    public function user($key = '',$token=null)
    {
        if (!empty($this->userInfo)) {
            if (!empty($key)) {
                return $this->userInfo[$key] ?? "";
            } else {
                return $this->userInfo;
            }
        }
        $token = $token??request()->header($this->tokenKey);
        if ($token === null) {
            error(__('Token Not Exists'));
        }
        try {
            $jwtAuth = json_encode(JWT::decode($token, $this->encrypt, array('HS256')));
            $authInfo = json_decode($jwtAuth, true);
            if (is_array($authInfo)) {
                $this->userInfo = $authInfo['user_info'];
                if (!empty($key)) {
                    return $authInfo['user_info'][$key] ?? "";
                } else {
                    return $authInfo['user_info'];
                }
            } else {
                error(__('Token Error'));
            }
        } catch (\Firebase\JWT\SignatureInvalidExceidption $e) {
            error(__('Token Error'));
        } catch (\Firebase\JWT\ExpiredException $e) {
            //redis方案
            //如果redis中有token 则直接更新令牌返回前段 要求redis存储token时间 大于前端存储时间 这样从redis中取用户ID
            //或者通过refresh_token 重新获取用户信息
            error(__('Token Expire'));
        } catch (\Exception $e) {
            error(__($e->getMessage()));
        }
    }

    /**
     * @param $userId
     * @author : yhc

     * @date: 2020/10/28 10:26
     * @name:
     */
    public function userRole($userId)
    {
        return AuthGroup::UserRole($userId);
    }

    /**
     * @param $userId
     * @param array $userGroupIds
     * @return array|bool|mixed
     * @author : yhc

     * @date: 2020/11/06 11:56
     * @name:
     */
    public function UserMenu($userId=null)
    {
        $userId = $userId ??$this->userInfo['id'];
        $cacheKey = 'Hnzl_AuthUserMenu' . $userId;
        if (!$userMenu = redis($cacheKey)) {
            $where = [
                ['status', '=', 0],
                ['is_show','=',0]
            ];
            if(!AuthGroup::isSupperUser($userId)){
                $userRoles = AuthGroup::UserRole($userId);
                $where[] =['id', 'in', $userRoles];
            }
            $userMenu = AuthRule::where($where)->field('id,pid,title,name,icon,ismenu,index,index_path')->order('weigh desc')->select();
            redis($cacheKey,$userMenu);
        }
        return $userMenu;
    }

    /**
     * @return array
     * @author : yhc

     * @date: 2021/03/04 09:53
     * @name: 用户登录站点
     */
    public function stations(){
        return  array_keys(\app\admin\logic\Station::station($this->userInfo['id']));
    }

    public function stationAll(){
        return  array_keys(\app\admin\logic\Station::station());
    }
}