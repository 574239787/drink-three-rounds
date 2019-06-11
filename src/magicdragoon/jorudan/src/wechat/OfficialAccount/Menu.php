<?php
namespace jorudan\wechat\OfficialAccount;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;
use jorudan\wechat\Traits\AccessToken;

/**
 * 公众号菜单处理
 *
 * @package jorudan\wechat\OfficialAccount
 * @author yaobin
 * @since 1.0
 */
class Menu extends Config
{
    use AccessToken;

    /**
     * 通过加密信息获取用户详情
     *
     * @param array $menu
     *
     * @return array
     */
    public function create($menu)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']) || empty($menu))
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

        $url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=" . $accessToken['access_token'];
        return json_decode(Util::urlRequest($url, $menu), true);
    }

}