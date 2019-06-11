<?php
namespace jorudan\wechat\Kernel;

/**
 * 配置接口
 *
 * @package jorudan\wechat\Kernel
 * @author yaobin
 * @since 1.0
 */
class Config
{
    /**
     * @var null|array
     */
    protected $_config = null;

    /**
     * @var null|Cache
     */
    protected $_cache = null;

    /**
     * 初始化
     *
     * @author yaobin
     * @since 1.0
     *
     * @param array $config 配置数组
     */
    public function __construct($config, Cache $cache = null)
    {
        $this->_config = $config;
        $this->_cache = $cache;
    }
}