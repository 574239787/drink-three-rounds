<?php
namespace jorudan\wechat\Traits;

use jorudan\wechat\Kernel\Util;

/**
 * Access Token Trait
 *
 * @package jorudan\wechat\Traits
 * @author yaobin
 * @since 1.0
 */
trait AccessToken
{

    /**
     * @var null|string Access Token
     */
    protected $_accessToken = null;

    /**
     * 设置Access Token
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->_accessToken = $accessToken;
    }

    /**
     * 获取Access Token
     *
     * @author yaobin
     * @since 1.0
     *
     * @return null|array
     */
    public function getAccessToken($appid, $secret)
    {
        if (empty($appid) || empty($secret))
        {
            return null;
        }

        if ($this->_cache != null && $this->_cache->exists('wx_access_token_' . $appid))
        {
            $this->_accessToken = $this->_cache->get('wx_access_token_' . $appid);
            if (!is_array($this->_accessToken) || !isset($this->_accessToken['expires_in'])
                || $this->_accessToken['expires_in'] < $_SERVER['REQUEST_TIME'])
            {
                $this->_accessToken = null;
            }
        }

        if ($this->_accessToken == null || $this->_accessToken['expires_in'] < $_SERVER['REQUEST_TIME'])
        {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" .
                $appid . "&secret=" . $secret;
            $return = Util::urlRequest($url);
            if (!$return)
            {
                return null;
            }
            $this->_accessToken = json_decode($return, true);
            if (!isset($this->_accessToken['access_token']) || !isset($this->_accessToken['expires_in']))
            {
                $this->_accessToken = null;
            }
            else
            {
                $this->_accessToken['expires_in'] += $_SERVER['REQUEST_TIME'];
            }
        }

        if ($this->_cache != null && $this->_accessToken != null)
        {
            $this->_cache->set('wx_access_token_' . $appid, $this->_accessToken);
        }

        return $this->_accessToken;
    }
}