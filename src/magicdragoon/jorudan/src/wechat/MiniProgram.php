<?php
namespace jorudan\wechat;

use jorudan\wechat\Kernel\ServiceContainer;

/**
 * 小程序容器
 *
 * @package jorudan\wechat
 * @author yaobin
 * @since 1.0
 *
 *
 * @property \jorudan\wechat\MiniProgram\User             $user
 * @property \jorudan\wechat\MiniProgram\Pay              $pay
 * @property \jorudan\wechat\MiniProgram\TemplateMessage  $templateMessage
 */
class MiniProgram extends ServiceContainer
{
    protected $_providers = [
        'user' => \jorudan\wechat\MiniProgram\User::Class,
        'pay' => \jorudan\wechat\MiniProgram\Pay::Class,
        'templateMessage' => \jorudan\wechat\MiniProgram\TemplateMessage::Class,
    ];
}