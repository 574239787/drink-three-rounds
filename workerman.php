<?php 

/**
* laravel框架加载workerman示例
* 引入workerman第三方库，具体步骤：假设workerman文件夹放在app目录下
* 在composer.json中的autoload下的classmap下加入"app/workerman"
* 执行composer dumpautoload
* php代码执行use Workerman\Worker;即可引入
*/

//引入workerman
use Workerman\Worker;
//引入workerman自定义类，这样就类似普通的controller控制器
use app\Http\Controllers\Workerman;
require_once __DIR__ . '/app/Workerman/Autoloader.php';

//载入WorkermanClass
require_once __DIR__ . '/app/Http/Controllers/Workerman.php';

// 注意：这里与上个例子不同，使用的是websocket协议
$ws_worker = new Worker("websocket://0.0.0.0:2346");

$ws_worker->count = 1;

$ws_worker->connection_uids=array();

// 创建一个对象
$my_object = new Workerman();
// 调用类的方法
$ws_worker->onWorkerStart = array($my_object, 'onWorkerStart');
$ws_worker->onConnect     = array($my_object, 'onConnect');
$ws_worker->onMessage     = array($my_object, 'onMessage');
$ws_worker->onClose       = array($my_object, 'onClose');
$ws_worker->onWorkerStop  = array($my_object, 'onWorkerStop');


// 如果类带命名空间，则是类似这样的写法
/*$worker->onWorkerStart = array('App\Http\Controllers\Workerman', 'onWorkerStart');
$worker->onConnect     = array('App\Http\Controllers\Workerman', 'onConnect');
$worker->onMessage     = array('App\Http\Controllers\Workerman', 'onMessage');
$worker->onClose       = array('App\Http\Controllers\Workerman', 'onClose');
$worker->onWorkerStop  = array('App\Http\Controllers\Workerman', 'onWorkerStop');*/

// 运行worker
Worker::runAll();
?>

<!-- 假设已在内容在App\Http\Controllers路径下 -->

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Workerman\Lib\Timer;

class Workerman
{
    public function __construct()
    {
        echo "string";
    }

    public function onWorkerStart($ws_worker)
    {

    }

    public function onConnect($connection)
    {
    }

    public function onMessage($connection, $data)
    {
        global $ws_worker;

        $d = json_decode($data);

        switch ($d->type) {
            case 'login':
                if (isset($ws_worker->connection_uids[$d->uid])) {
                    $conn = $ws_worker->connection_uids[$d->uid];
                    $conn->send( json_encode(['status' => 0, ',message' => '已经登录！']) );
                    echo "已经登录,uid={$d->uid}\n";
                } else {
                    $connection->uid = $d->uid;
                    $ws_worker->connection_uids[$d->uid] = $connection;
                    $ws_worker->connection_uids[$d->uid]->send( json_encode(['status' => 1, ',message' => '登录成功！']) );
                    echo "收到登录请求,uid={$d->uid}\n";
                }
                break;
            case 'send_message':
                if ($d->to == 'all') {
                    echo "向全部用户发送消息\n";
                    foreach ($ws_worker->connections as $conn) {
                        $conn->send($d->message);
                    }
                } else {
                    echo "向用户{$d->to}发送消息\n";
                    if (isset($ws_worker->connection_uids[$d->to])) {
                        $conn = $ws_worker->connection_uids[$d->to];
                        $conn->send($d->message);
                    }
                }
                break;
            default:
                break;

        }
    }

    public function onClose($connection)
    {
        global $ws_worker;
        if (isset($connection->uid)) {
            // 连接断开时删除映射
            unset($ws_worker->uidConnections[$connection->uid]);
            unset($ws_worker->connection_uids[$connection->uid]);
        }
    }

    public function onWorkerStop($connection)
    {
    }
}
