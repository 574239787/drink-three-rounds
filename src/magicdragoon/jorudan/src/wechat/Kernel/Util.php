<?php
namespace jorudan\wechat\Kernel;

/**
 * 工具类
 *
 * @package jorudan\wechat\Kernel
 * @author yaobin
 * @since 1.0
 */
class Util
{
    /**
     * curl
     *
     * @author yaobin
     * @since 1.0
     *
     * @param string $url
     * @param null|string $data
     * @param float $data
     *
     * @return bool|mixed
     */
    public static function urlRequest($url, $data = NULL, $useCert = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        if (substr($url, 0, 5) == 'https')
        {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

            //if($useCert == true){
                //设置证书
                //第一种方法，cert 与 key 分别属于两个.pem文件
                //curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
                //curl_setopt($ch,CURLOPT_SSLCERT, SSLCERT_PATH);
                //curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
                //curl_setopt($ch,CURLOPT_SSLKEY, SSLKEY_PATH);

                //第二种方式，两个文件合成一个.pem文件
                //curl_setopt($ch,CURLOPT_SSLCERT, SSLALL_PATH);
            //}
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($data))
        {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (is_array($data)) {
                $data = json_encode($data, JSON_UNESCAPED_UNICODE);
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!$result = curl_exec($ch))
        {
            $result = false;
        }

        curl_close($ch);

        return $result;
    }

    /**
     * 解密AES加密信息
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $appid
     * @param $sessionKey
     * @param $encryptedData
     * @param $iv
     * @return bool|string
     */
    public static function decryptAesData($appid, $sessionKey, $encryptedData, $iv)
    {
        if (strlen($sessionKey) != 24)
        {
            return false;
        }
        $aesKey = base64_decode($sessionKey);


        if (strlen($iv) != 24)
        {
            return ErrorCode::$IllegalIv;
        }
        $aesIV = base64_decode($iv);

        $aesCipher = base64_decode($encryptedData);

        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);

        $result = json_decode($result, true);
        if ($result == NULL || !isset($result['unionId']) || $result['watermark']['appid'] != $appid)
        {
            return false;
        }
        return $result;
    }

    /**
     * 数组转XML字符串
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $array
     *
     * @return string
     * @throws \Exception
     */
    public static function arrayToXml($array)
    {
        if (!is_array($array) || count($array) <= 0)
        {
            throw new \Exception("数组数据异常！");
        }

        $xml = "<xml>";
        foreach ($array as $key=>$val)
        {
            $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     * XMl字符串转数组
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $xml
     *
     * @return mixed
     * @throws \Exception
     */
    public static function xmlToArray($xml)
    {
        if(!$xml){
            throw new \Exception("xml数据异常！");
        }
        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 数组转URL参数
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $array
     *
     * @return string
     */
    public function arrayToUrlParams($array)
    {
        $buff = "";
        foreach ($array as $k => $v)
        {
            if ($k != "sign" && $v != "" && !is_array($v))
            {
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

    /**
     * 获取加密字符串
     *
     * @param array $array
     * @param $key
     * @param string $encryptMethod
     * @return string
     */
    public static function generateSign(array $array, $key, $encryptMethod = 'md5')
    {
        ksort($array);

        $array['key'] = $key;

        return strtoupper(call_user_func_array($encryptMethod, [urldecode(http_build_query($array))]));
    }

    /**
     * 生成唯一码
     *
     * @author yaobin
     * @since 1.0
     *
     * @param $length
     *
     * @return string
     */
    public static function getNonceStr($length = 32)
    {
//        return uniqid();
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 获取客户端IP
     *
     * @author yaobin
     * @since 1.0
     *
     * @return string
     */
    public static function getClientIp()
    {
        if (!empty($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    /**
     * 获取服务器IP
     *
     * @author yaobin
     * @since 1.0
     *
     * @return string
     */
    public static function getServerIp()
    {
        if (!empty($_SERVER['SERVER_ADDR']))
        {
            $ip = $_SERVER['SERVER_ADDR'];
        }
        elseif (!empty($_SERVER['SERVER_NAME']))
        {
            $ip = gethostbyname($_SERVER['SERVER_NAME']);
        }
        else
        {
            // for php-cli(phpunit etc.)
            $ip = defined('PHPUNIT_RUNNING') ? '127.0.0.1' : gethostbyname(gethostname());
        }

        return filter_var($ip, FILTER_VALIDATE_IP) ?: '127.0.0.1';
    }

    /**
     * 获取当前URL
     *
     * @author yaobin
     * @since 1.0
     *
     * @return string
     */
    public static function getCurrentUrl()
    {
        $protocol = 'http://';

        if (!empty($_SERVER['HTTPS']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'))
        {
            $protocol = 'https://';
        }

        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * 获取Xml Input数据
     *
     * @return bool|array
     */
    public static function getXmlBody()
    {
        $body = trim(file_get_contents('php://input'));
        if (empty($body) || !isset($body[0]) || $body[0] != '<')
        {
            return false;
        }
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(
            simplexml_load_string($body, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    /**
     * 获取Jsom Input数据
     *
     * @return bool|array
     */
    public static function getJsonBody()
    {
        $body = trim(file_get_contents('php://input'));
        if (empty($body) || !isset($body[0]) || ($body[0] != '{' && $body[0] != '['))
        {
            return false;
        }
        return json_decode($body, true);
    }
}