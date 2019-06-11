<?php
namespace jorudan\wechat\MiniProgram;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;

/**
 * 小程序用户处理
 *
 * @package jorudan\wechat\MiniProgram
 * @author yaobin
 * @since 1.0
 */
class User extends Config
{
    /**
     * 通过加密信息获取用户详情
     *
     * @param string $encryptedData
     * @param string $iv
     * @param string $code
     * @return null|array
     */
    public function getInfoByCode($encryptedData, $iv, $code)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $url = "https://api.weixin.qq.com/sns/jscode2session?";
        $url .= "appid=" . $this->_config['appid'] . "&secret=" . $this->_config['secret'] .
            "&js_code=" . $code . "&grant_type=authorization_code";
        $return = Util::urlRequest($url);
        if (!$return)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常!',
            ];
        }
        $return = json_decode($return, true);
        if (!isset($return['openid']) || !isset($return['session_key']))
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
            ];
        }

        $return = Util::decryptAesData($this->_config['appid'], $return['session_key'], $encryptedData, $iv);
        if (!$return)
        {
            return [
                'errcode' => 4,
                'errmsg' => '系统异常!',
            ];
        }

        return $return;
    }
}