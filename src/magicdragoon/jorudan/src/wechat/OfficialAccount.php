<?php
namespace jorudan\wechat;

use jorudan\wechat\Kernel\ServiceContainer;

/**
 * 公众号容器
 *
 * @package jorudan\wechat
 * @author yaobin
 * @since 1.0
 *
 * @property \jorudan\wechat\OfficialAccount\User             $user
 * @property \jorudan\wechat\OfficialAccount\Menu             $menu
 * @property \jorudan\wechat\OfficialAccount\TemplateMessage  $templateMessage
 */
class OfficialAccount extends ServiceContainer
{
    protected $_providers = [
        'user' => \jorudan\wechat\OfficialAccount\User::class,
        'menu' => \jorudan\wechat\OfficialAccount\Menu::class,
        'templateMessage' => \jorudan\wechat\OfficialAccount\TemplateMessage::class,
    ];
}