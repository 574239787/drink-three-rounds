<?php
namespace jorudan\wechat\Kernel;

/**
 * 服务容器
 *
 * @package jorudan\wechat\Kernel
 * @author yaobin
 * @since 1.0
 */
class ServiceContainer extends Config
{
    /**
     * 提供者列表
     *
     * @var array
     */
    protected $_providers = [];

    /**
     * 服务列表
     *
     * @var array
     */
    protected $_services = [];

    /**
     * Magic get access.
     *
     * @author yaobin
     * @since 1.0
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        if (isset($this->_services[$id]))
        {
            return $this->_services[$id];
        }

        if (!isset($this->_providers[$id]))
        {
            throw new \Exception('Service definition is not a Closure or invokable object.');
        }

        if (!is_subclass_of($this->_providers[$id], Config::class))
        {
            unset($this->_providers[$id]);
            throw new \Exception('Service definition is not sub class of Config class.');
        }

        $this->_services[$id] = new $this->_providers[$id]($this->_config, $this->_cache);

        return $this->_services[$id];
    }
}