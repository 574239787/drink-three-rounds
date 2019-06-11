<?php
namespace jorudan\wechat\OfficialAccount;

use Yii;
use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;
use jorudan\wechat\Traits\AccessToken;

/**
 * 公众号模板消息处理
 *
 * @package jorudan\wechat\OfficialAccount
 * @author yaobin
 * @since 1.0
 */
class TemplateMessage extends Config
{
    use AccessToken;

    /**
     * 发送模板消息
     *
     * @param string $templateid  模板ID
     * @param string $formid      formid
     * @param array  $data        模板内容
     * @param string $page        点击模板卡片后的跳转页面
     * @param string $appid       跳转的小程序appid
     */
    public function send($templateid, $openid, $data, $page = null, $appid = null)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $access_token = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($access_token == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $access_token['access_token'];

        $params = [
            'touser' => $openid,
            'template_id' => $templateid,
            'data' => $data,
        ];
        if ($page != null)
        {
            $params['url'] = $page;
            if ($appid != null)
            {
                $params['miniprogram'] = [
                    'appid' => $appid,
                    'pagepath' => $page
                ];
            }
        }

        $return = Util::urlRequest($url, $params);
        if (!$return)
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
            ];
        }

        $return = json_decode($return, true);

        if (!isset($return['errcode']))
        {
            return [
                'errcode' => 4,
                'errmsg' => '发送失败!',
                'data' => $return,
            ];
        }
        else
        {
            if ($return['errcode'] != 0)
            {
                return $return;
            }
        }

        return [
            'result' => true,
            'return' => $return
        ];
    }
}