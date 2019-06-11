<?php
namespace jorudan\wechat\MiniProgram;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;

/**
 * 小程序支付处理
 *
 * @package jorudan\wechat\MiniProgram
 * @author yaobin
 * @since 1.0
 */
class Pay extends Config
{
    /**
     * 统一下单接口
     *
     * @param string $openid     用户ID
     * @param string $body       商品描述
     * @param string $tradeNo    商户订单号
     * @param float  $totalFee   总金额
     * @param string $notifyUrl  通知地址
     * @return null|string
     */
    public function unifiedorder($openid, $body, $tradeNo, $totalFee, $notifyUrl, $trade_type = "JSAPI")
    {
        if (!isset($this->_config['pay_appid']) || !isset($this->_config['mch_id']) || !isset($this->_config['pay_key']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $param = [
            'appid' => $this->_config['pay_appid'],
            'mch_id' => $this->_config['mch_id'],
            'nonce_str' => Util::getNonceStr(),
            'body' => $body,
            'out_trade_no' => $tradeNo,
            'total_fee' => $totalFee,
            'spbill_create_ip' => Util::getClientIp(),
            'notify_url' => $notifyUrl,
            'trade_type' => $trade_type
        ];

        if ($trade_type == 'JSAPI') {
            $param['openid'] = $openid;
        }
        
        if ($trade_type == 'NATIVE') {
            $param['product_id'] = $tradeNo;
        }

        $param['sign'] = Util::generateSign($param, $this->_config['pay_key']);

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

        $return = Util::urlRequest($url, Util::arrayToXml($param));
        if (!$return)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常!',
            ];
        }

        $return = Util::xmlToArray($return);
        if (!isset($return['return_code']) || $return['return_code'] != 'SUCCESS'
            || !isset($return['result_code']) || $return['result_code'] != 'SUCCESS')
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
                'data' => $return,
            ];
        }

        if ($trade_type == 'MWEB') {
            $payParam['mweb_url'] = $return['mweb_url'];
        } elseif ($trade_type == 'NATIVE') {
            $payParam['code_url'] = $return['code_url'];
        } else {
            $payParam = [
                'timeStamp' => $_SERVER['REQUEST_TIME'] . "",
                'nonceStr' => Util::getNonceStr(),
                'package' => 'prepay_id=' . $return['prepay_id'],
                'signType' => 'MD5',
            ];
            
            $payParam['paySign'] = $this->getPaySign($payParam);
        }
        return $payParam;
    }

    /**
     * 查询订单接口
     *
     * @param string $tradeNo    商户订单号
     * @return null|string
     */
    public function orderquery($tradeNo)
    {
        if (!isset($this->_config['pay_appid']) || !isset($this->_config['mch_id']) || !isset($this->_config['pay_key']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $param = [
            'appid' => $this->_config['pay_appid'],
            'mch_id' => $this->_config['mch_id'],
            'nonce_str' => Util::getNonceStr(),
            'out_trade_no' => $tradeNo,
        ];

        $param['sign'] = Util::generateSign($param, $this->_config['pay_key']);

        $url = "https://api.mch.weixin.qq.com/pay/orderquery";

        $return = Util::urlRequest($url, Util::arrayToXml($param));
        if (!$return)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常!',
            ];
        }

        $return = Util::xmlToArray($return);
        if (!isset($return['return_code']) || $return['return_code'] != 'SUCCESS'
            || !isset($return['result_code']) || $return['result_code'] != 'SUCCESS')
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
                'data' => $return,
            ];
        }

        return $return;
    }

    /**
     * 申请退款接口
     *
     * @param string $tradeNo    商户订单号
     * @return null|string
     */
    public function refund($tradeNo, $refund, $total)
    {
        if (!isset($this->_config['pay_appid']) || !isset($this->_config['mch_id']) || !isset($this->_config['pay_key']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $param = [
            'appid' => $this->_config['pay_appid'],
            'mch_id' => $this->_config['mch_id'],
            'nonce_str' => Util::getNonceStr(),
            'out_trade_no' => $tradeNo,
            'out_refund_no' => $refund,
            'refund_fee' => $refund,
            'total_fee' => $total,
        ];

        $param['sign'] = Util::generateSign($param, $this->_config['pay_key']);

        $url = "https://api.mch.weixin.qq.com/secapi/pay/refund";

        $return = Util::urlRequest($url, Util::arrayToXml($param));
        if (!$return)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常!',
            ];
        }

        $return = Util::xmlToArray($return);
        if (!isset($return['return_code']) || $return['return_code'] != 'SUCCESS'
            || !isset($return['result_code']) || $return['result_code'] != 'SUCCESS')
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
                'data' => $return,
            ];
        }
        
        return $return;
    }

    protected function getPaySign($param)
    {
        $param['appId'] = $this->_config['pay_appid'];
        return Util::generateSign($param, $this->_config['pay_key']);
    }
}