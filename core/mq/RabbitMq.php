<?php
namespace core\mq;

use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**

$Mq = $this->di->getMq();

# 生产消息
$Mq->push('Some Message');

# 获取单条消息
$message = $Mq->get();
if($message) {
$Mq->ack($message);
} else {
$Mq->nack($message);
}
var_dump($message);

# rpc远程调用
$json = json_encode(['module'=>'', 'action'=>'', 'params'=>[]]);
$response = $Mq->call($json);
var_dump($response);

# 拉取消息
$callback = [$this, 'method_name'];
$Mq->pull($callback);

*/

class RabbitMq extends Mq
{

    /**
     * @var mixed
     */
    private static $conn = null;

    // 已创建过的连接管道，存在则无需重复连接
    /**
     * @var array
     */
    private static $channels = [];

    // 已申明过的交换机，存在则无需重复申明
    /**
     * @var array
     */
    private static $exchanges = [];

    // 已申明过的队列映射，存在则无需重复申明
    /**
     * @var array
     */
    private static $queues = [];

    // 已绑定过的路由
    /**
     * @var array
     */
    private static $bindings = [];

    /**
     * @var array
     *
     */
    private static $loggers = [];

    /** 传送的属性配置参数
    [
    'message'=>[..消息体属性..],
    'queue'=>[..队列参数..],
    'exchange'=>[..交换机参数..],
    'consume'=>[..消费参数..],
    ]
     */
    private $default_properties = [
        'confirm' => true,
        'transaction' => false,
        'exchange' => [],
        'queue' => [],
        'message' => [],
        'consume' => [],
        'binding_keys' => [],
    ];

    /**
     * @var mixed
     */
    private $properties;

    //连接默认参数，配置在config.mq_config.rabbitmq
    /**
     * @var array
     */
    private $conn_properties = [
        'env_prefix' => 'local_',
        'insist' => false,
        'login_method' => 'AMQPLAIN',
        'login_response' => null,
        'locale' => 'en_US',
        'connection_timeout' => 3.0,
        'read_write_timeout' => 3.0,
        'context' => null,
        'keepalive' => false,
        'heartbeat' => 0,
    ];

    //申明交换机默认值，通过publish、consume方法传递参数properties.exchange
    /**
     * @var array
     */
    private $exchange_properties = [
        'type' => 'nameless', //默认为匿名交换机
        'passive' => false,
        'durable' => true,
        'auto_delete' => false,
    ];

    //申明队列默认参数，通过publish、consume方法传递参数properties.queue
    /**
     * @var array
     */
    private $queue_properties = [
        'passive' => false,
        'durable' => true,
        'exclusive' => false,
        'auto_delete' => false,
        'nowait' => false,
    ];

    //申明消息体默认属性，通过publish方法传递参数properties.message
    /**
     * @var array
     */
    private $message_properties = [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
    ];

    //消费默认参数，通过consume方法传递参数properties.consume
    /**
     * @var array
     */
    private $consume_properties = [
        'consumer_tag' => '',
        'no_local' => false,
        'no_ack' => false,
        'exclusive' => false,
        'nowait' => false,
        'qos' => 10,
        'time_out' => 600,
    ];

    /**
     * @var mixed
     */
    private $rpc_response;
    /**
     * @var mixed
     */
    private $rpc_correlation_id;
    /**
     * @var mixed
     */
    private $rpc_callback_queue;
    /**
     * @var string
     */
    private $rpc_queue = 'rpc_queue';

    /**
     * @var string
     */
    private static $env_prefix = '';
    private static $log_path = '/tmp/';
    /**
     * @var int
     */
    private static $daytime = 0;
    /**
     * @var mixed
     */
    private static $confirm_selected = false;
    /**
     * @var mixed
     */
    private $confirm_published = false;

    const RPC_CALL_STATUS_PUBLISHED = 0;
    const RPC_CALL_STATUS_RESPONSED = 1;

    public function __construct()
    {
        $this->properties = $this->default_properties;

        if (self::$conn instanceof AMQPStreamConnection) {
            return self::$conn;
        }
        
        $config = $this->di->get('config')->get('rabbit_mq_list');
        $conn_properties = array_merge($this->conn_properties, $config);
        extract($conn_properties);
        if (isset($env_prefix)) {
            self::$env_prefix = $env_prefix;
        }
        if (isset($log_path)) {
            self::$log_path = $log_path;
        }

        self::$conn = new AMQPStreamConnection(
            $host,
            $port,
            $user,
            $password,
            $vhost,
            $insist,
            $login_method,
            $login_response,
            $locale,
            $connection_timeout,
            $read_write_timeout,
            $context,
            $keepalive,
            $heartbeat
        );

        $RabbitMq = $this;
        $this->channel()->set_ack_handler(
            function (AMQPMessage $message) use ($RabbitMq) {
                $RabbitMq->set_ack_handler($message);
            }
        );

        $this->channel()->set_nack_handler(
            function (AMQPMessage $message) use ($RabbitMq) {
                $RabbitMq->set_nack_handler($message);
            }
        );
    }

    /**
     * 管道连接
     * @param  integer $channel_id [description]
     * @return [type]              [description]
     */
    public function channel($channel_id = 1)
    {
        if (!isset(self::$channels[$channel_id])) {
            self::$channels[$channel_id] = self::$conn->channel($channel_id);
        }

        return self::$channels[$channel_id];
    }

    public function getRpcQueue()
    {
        return self::$env_prefix . $this->rpc_queue;
    }

    /**
     * 申明消息体
     * @param  [type] $body [description]
     * @return [type]       [description]
     */
    public function messageDeclare($body)
    {
        $message_properties = $this->message_properties;

        if (isset($this->properties['message'])) {
            $message_properties = array_merge($message_properties, $this->properties['message']);
        }

        return new AMQPMessage($body, $message_properties);
    }

    /**
     * 取消拉取数据
     * @param  [type] $msg [description]
     * @return [type]      [description]
     */
    public function cancelPull($msg)
    {
        return $msg->delivery_info['channel']->basic_cancel($msg->delivery_info['consumer_tag']);
    }

    /**
     * @param $name
     */
    public function getLogger($name)
    {
        $current_time = date('Ymd');
        $file_path = self::$log_path . "xhh_mq_%d.log";

        if (!isset(self::$loggers[$name]) || $current_time != self::$daytime) {
            $log_file = sprintf($file_path, $current_time);
            $stream = new StreamHandler($log_file, Logger::INFO);
            $stream->setFormatter(new LogstashFormatter($name));
            self::$loggers[$name] = new \Monolog\Logger($name);
            self::$loggers[$name]->pushHandler($stream);
            self::$daytime = $current_time;
        }

        return self::$loggers[$name];
    }

    /**
     * @param $name
     * @param $message
     * @param array $context
     * @param $level
     */
    public function log($name, $message, $context = [], $level = 'info')
    {
        $callback = [$this->getLogger($name), $level];

        if (!is_callable($callback, false, $callable_name)) {
            throw new \Exception("{$callable_name}不可调用");
        }

        return call_user_func_array($callback, [$message, $context]);
    }

    /**
     * @param AMQPMessage $message
     */
    public function set_ack_handler(AMQPMessage $message)
    {
        $this->confirm_published = true;
    }

    /**
     * @param AMQPMessage $message
     */
    public function set_nack_handler(AMQPMessage $message)
    {
        $this->confirm_published = false;
    }

    /**
     * 申明交换机
     * @param  [type] $exchange [description]
     * @return [type]           [description]
     */
    private function exchangeDeclare(&$exchange)
    {
        if (!empty($exchange)) {
            $exchange = self::$env_prefix . $exchange;
        }

        $exchange_properties = $this->exchange_properties;
        if (isset($this->properties['exchange'])) {
            $exchange_properties = array_merge($exchange_properties, $this->properties['exchange']);
        }

        //判断是否已申明过
        $key = md5($exchange . serialize($exchange_properties));
        if (isset(self::$exchanges[$key])) {
            return self::$exchanges[$key];
        }

        extract($exchange_properties);
        //判断是否是匿名交换机
        if ($type != 'nameless' && !empty($exchange)) {
            $this->channel()->exchange_declare($exchange, $type, $passive, $durable, $auto_delete);
            self::$exchanges[$key] = true;

            return self::$exchanges[$key];
        } else {
            return false;
        }
    }

    /**
     * 绑定队列
     * @return [type] [description]
     */
    private function queueBind($exchange, $queue = '')
    {
        if (empty($queue)) {
            $queue_name = "";
            list($queue_name) = $this->queueDeclare($queue_name);
        } else {
            $this->queueDeclare($queue);
            $queue_name = $queue;
        }

        if (isset($this->properties['binding_keys']) && !empty($this->properties['binding_keys'])) {
            if (is_array($this->properties['binding_keys'])) {
                foreach ($this->properties['binding_keys'] as $binding_key) {
                    $this->routeBind($queue_name, $exchange, $binding_key);
                }
            } else {
                $this->routeBind($queue_name, $exchange, $this->properties['binding_keys']);
            }
        } else {
            $this->routeBind($queue_name, $exchange, $queue_name);
        }

        return $queue_name;
    }

    /**
     * @param $queue
     * @param $exchange
     * @param $binding_key
     */
    private function routeBind($queue, $exchange, $binding_key)
    {
        $key = md5("{$queue}.{$exchange}.{$binding_key}");
        if (!isset(self::$bindings[$key])) {
            $this->channel()->queue_bind($queue, $exchange, $binding_key);
            self::$bindings[$key] = true;
        }

        return self::$bindings[$key];
    }

    /**
     * 申明队列
     * @param  [type] $queue [description]
     * @return [type]        [description]
     */
    private function queueDeclare(&$queue)
    {
        if (!empty($queue)) {
            $queue = self::$env_prefix . $queue;
        }

        $queue_properties = $this->queue_properties;
        if (isset($this->properties['queue'])) {
            $queue_properties = array_merge($queue_properties, $this->properties['queue']);
        }

        //判断是否已申明过
        $key = md5($queue . serialize($queue_properties));
        if (isset(self::$queues[$key])) {
            return self::$queues[$key];
        }

        extract($queue_properties);
        self::$queues[$key] = $this->channel()->queue_declare($queue, $passive, $durable, $exclusive, $auto_delete, $nowait);

        return self::$queues[$key];
    }

    /**
     * PUSH ACTION
     * @param  [type] $message  [description]
     * @param  [type] $exchange [description]
     * @param  [type] $queue    [description]
     * @return [type]           [description]
     */
    private function pushAction($message, $exchange, $queue)
    {
        try {
            $this->confirm_published = false;

            if (isset($this->properties['transaction']) && $this->properties['transaction']) {
                $this->channel()->tx_select();
            } elseif (isset($this->properties['confirm']) && $this->properties['confirm'] && !self::$confirm_selected) {
                $this->channel()->confirm_select();

                // confirm只能调用一次
                self::$confirm_selected = true;
            }

            $this->channel()->basic_publish($message, $exchange, $queue);

            if (isset($this->properties['transaction']) && $this->properties['transaction']) {
                $this->channel()->tx_commit();
            } elseif (isset($this->properties['confirm']) && $this->properties['confirm']) {
                $this->channel()->wait_for_pending_acks();
            }

            $result = true;
            if (self::$confirm_selected) {
                $result = $this->confirm_published;
            }

            //记录消息生产日志
            $this->log('xhh.mq.push', $message->getBody(), [
                'queue' => $queue,
                'exchange' => $exchange,
                'confirm_selected' => self::$confirm_selected,
                'confirm_published' => $this->confirm_published,
                'properties' => $this->properties,
            ], $result ? 'info' : 'error');

            return $result;

        } catch (\Exception $e) {
            if (isset($this->properties['transaction']) && $this->properties['transaction']) {
                $this->channel()->tx_rollback();
            }

            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $rep
     */
    public function onRpcResponse($rep)
    {
        if ($rep->get('correlation_id') == $this->rpc_correlation_id) {
            $this->rpc_response = $rep->body;

            //记录rpc调用日志
            $this->log('xhh.mq.call', $this->rpc_response, [
                'rpc_queue' => $this->getRpcQueue(),
                'status' => self::RPC_CALL_STATUS_RESPONSED,
                'correlation_id' => $this->rpc_correlation_id,
                'reply_to' => $this->rpc_callback_queue,
            ], $this->rpc_response ? 'info' : 'error');
        }
    }

    /**
     * 发送消息队列
     * @param  [type] $body       消息内容
     * @param  [type] $queue      队列名
     * @param  [type] $exchange   交换机名称
     * @param  array  $properties 其他配置属性
     * @return [type]             [description]
     */
    public function push($body, $queue = 'xhh_default_queue', $exchange = '', $properties = [])
    {
        $this->properties = array_merge($this->default_properties, $properties);

        $exchange_declared = $this->exchangeDeclare($exchange);
        if (!$exchange_declared) {
            $this->queueDeclare($queue);
        } elseif (!empty($queue)) {
            $this->queueBind($exchange, $queue);
        }

        return $this->pushAction($this->messageDeclare($body), $exchange, $queue);
    }

    /**
     * 接收消息队列
     * @param  [type] $callback 消息回调函数
     * @param  string $queue    队列名
     * @param  string $exchange   交换机名称
     * @param  array  $properties 其他配置属性
     * @return [type]           [description]
     */
    public function pull($callback, $queue = 'xhh_default_queue', $exchange = '', $properties = [])
    {
        $this->properties = array_merge($this->default_properties, $properties);

        if (!is_callable($callback, false, $callable_name)) {
            throw new \Exception("{$callable_name}不可调用");
        }

        try {
            $exchange_declared = $this->exchangeDeclare($exchange);
            if (!$exchange_declared) {
                $this->queueDeclare($queue);
            } else {
                $queue = $this->queueBind($exchange, $queue);
            }

            $consume_properties = $this->consume_properties;
            if (isset($this->properties['consume'])) {
                $consume_properties = array_merge($consume_properties, $this->properties['consume']);
            }

            extract($consume_properties);
            $RabbitMq = $this;

            // 定义回调处理函数
            $callback_func = function ($msg) use ($callback, $RabbitMq, $queue, $exchange, $no_ack, $properties) {

                $handle_result = call_user_func_array($callback, array($msg, $RabbitMq));

                //RPC回调
                $rpc_call = false;
                $correlation_id = '';
                if ($msg->has('reply_to') && $msg->has('correlation_id')) {
                    $this->properties['message'] = [
                        'correlation_id' => $msg->get('correlation_id'),
                    ];

                    $msg->delivery_info['channel']->basic_publish(
                        $this->messageDeclare($handle_result),
                        '',
                        $msg->get('reply_to')
                    );

                    $rpc_call = true;
                    $correlation_id = $msg->get('correlation_id');
                }

                //记录消息消费日志
                $this->log('xhh.mq.pull', $msg->body, [
                    'handle_result' => $handle_result ? true : false,
                    'no_ack' => $no_ack,
                    'queue' => $queue,
                    'exchange' => $exchange,
                    'rpc_call' => $rpc_call,
                    'correlation_id' => $correlation_id,
                    'properties' => $properties,
                ], $handle_result ? 'info' : 'error');

                if ($no_ack === false) {
                    if ($handle_result) {
                        $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
                    } else {
                        $msg->delivery_info['channel']->basic_nack($msg->delivery_info['delivery_tag'], false, true);
                    }
                }
            };

            //公平调度
            if ($qos > 0) {
                $this->channel()->basic_qos(null, $qos, null);
            }

            $this->channel()->basic_consume($queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait, $callback_func);

            while (count($this->channel()->callbacks)) {
                $this->channel()->wait(null, false, $time_out);
            }

            $this->safe_close();
        } catch (\Exception $e) {
            $this->safe_close();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * 获取单条消息
     * @param  string  $queue  [description]
     * @param  boolean $no_ack [description]
     * @param  [type]  $ticket [description]
     * @return [type]          [description]
     */
    public function get($queue = 'xhh_default_queue', $exchange = '', $properties = [])
    {
        $this->properties = array_merge($this->default_properties, $properties);

        $exchange_declared = $this->exchangeDeclare($exchange);
        if (!$exchange_declared) {
            $this->queueDeclare($queue);
        } else {
            $queue = $this->queueBind($exchange, $queue);
        }

        $message = $this->channel()->basic_get($queue);

        //记录消息获取日志
        $this->log('xhh.mq.get', $message ? $message->body : '', [
            'queue' => $queue,
            'exchange' => $exchange,
            'properties' => $properties,
        ]);

        return $message;
    }

    /**
     * 消费成功确认
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function ack($message)
    {
        $this->channel()->basic_ack($message->delivery_info['delivery_tag']);
    }

    /**
     * 未消费确认
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function nack($message)
    {
        $this->channel()->basic_nack($message->delivery_info['delivery_tag'], false, true);
    }

    /**
     * RPC远程调用客户端接口
     * @param  [type] $body [description]
     * @return [type]       [description]
     */
    public function call($body, $rpc_queue = 'rpc_queue', $properties = [])
    {
        $this->properties = array_merge($this->default_properties, $properties);
        $this->rpc_response = null;
        $this->rpc_correlation_id = uniqid(mt_rand(10000, 99999));

        // 生成一个随机接收的排他队列
        $this->properties['queue']['exclusive'] = true;

        $rpc_callback_queue = '';
        $this->rpc_queue = $rpc_queue;
        list($this->rpc_callback_queue) = $this->queueDeclare($rpc_callback_queue);

        $consume_properties = $this->consume_properties;
        if (isset($this->properties['consume'])) {
            $consume_properties = array_merge($consume_properties, $this->properties['consume']);
        }
        extract($consume_properties);
        $this->channel()->basic_consume(
            $this->rpc_callback_queue, $consumer_tag, $no_local, $no_ack, $exclusive, $nowait,
            array($this, 'onRpcResponse')
        );

        // 定义额外消息体属性
        $message_properties = [
            'message' => [
                'correlation_id' => $this->rpc_correlation_id,
                'reply_to' => $this->rpc_callback_queue,
            ],
            'queue' => ['exclusive' => false],
        ];
        $this->properties = array_merge($this->properties, $message_properties);

        $msg = $this->messageDeclare($body);
        $this->queueDeclare($rpc_queue);
        $this->channel()->basic_publish($msg, '', $this->getRpcQueue());

        //记录rpc调用日志
        $this->log('xhh.mq.call', $msg->getBody(), [
            'rpc_queue' => $this->getRpcQueue(),
            'status' => self::RPC_CALL_STATUS_PUBLISHED,
            'correlation_id' => $this->rpc_correlation_id,
            'reply_to' => $this->rpc_callback_queue,
        ]);

        while (!$this->rpc_response) {
            $this->channel()->wait(null, false, $time_out);
        }

        return $this->rpc_response;
    }

    public function safe_close()
    {
        if (self::$conn instanceof AMQPStreamConnection) {
            self::$conn->close();
            self::$conn = null;
            self::$channels = [];
        }
    }

    public function __destruct()
    {
        $this->safe_close();
    }
}
