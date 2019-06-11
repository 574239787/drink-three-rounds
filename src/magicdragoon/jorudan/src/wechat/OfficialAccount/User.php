<?php
namespace jorudan\wechat\OfficialAccount;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;
use jorudan\wechat\Traits\AccessToken;

/**
 * 公众号用户处理
 *
 * @package jorudan\wechat\OfficialAccount
 * @author yaobin
 * @since 1.0
 */
class User extends Config
{
    use AccessToken;

    public function getInfoByOpenID($openid)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" . $accessToken['access_token'] .
            "&lang=zh_CN&openid=" . $openid;
        return json_decode(Util::urlRequest($url), true);
    }

    /**
     * 通过加密信息获取用户详情
     *
     * @return null|array
     */
    public function getList($next = null)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/user/get?access_token=" . $accessToken['access_token'];
        if ($next != null)
        {
            $url .= '&next_openid=' . $next;
        }
        $return = json_decode(Util::urlRequest($url), true);
        if (isset($return['errcode']))
        {
            return $return;
        }

        if (!isset($return['total']) || !isset($return['count']) || !isset($return['data'])
            || !isset($return['next_openid']))
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }
        return $return;
    }

    public function getInfoByList($list)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $userList = [];
        foreach ($list as $openid)
        {
            $userList[] = ['openid'=>$openid];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/user/info/batchget?access_token=" . $accessToken['access_token'];
        $return = json_decode(Util::urlRequest($url, ['user_list'=>$userList]), true);
        if (isset($return['errcode']))
        {
            return $return;
        }

        if (!isset($return['user_info_list']))
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }
        return $return['user_info_list'];
    }

    public function getTokenByCode($code) {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=" . $this->_config['appid'] . 
        "&secret=" . $this->_config['secret'] . "&code=" . $code . "&grant_type=authorization_code";
        return json_decode(Util::urlRequest($url), true);
    }
}