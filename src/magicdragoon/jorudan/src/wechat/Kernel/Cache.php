<?php
namespace jorudan\wechat\Kernel;

/**
 * 缓存接口
 *
 * @package jorudan\wechat\Kernel
 * @author yaobin
 * @since 1.0
 */
interface Cache
{
    /**
     * 获取缓存值
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * 查询指定缓存是否存在
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $key
     * @return bool
     */
    public function exists($key);

    /**
     * 设置缓存
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $key
     * @param $value
     * @param $duration
     * @return bool
     */
    public function set($key, $value, $duration = null);
}