<?php
namespace core\base;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Noodlehaus\Config as NoodConfig;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use core\base\Components;
use core\base\Router;
use core\curl\CurlRequest;
use core\base\mq\MqFactory;

/**
 * 注册框架需要的核心服务组件
 */
class BaseApp extends Components
{
	/**
	 * 服务管理容器
	 */
	protected $di;

	/**
	 * 注册配置服务，同时绑定core服务
	 */
	public function __construct($config_path)
	{
		$this->di = new FactoryDefault();
		$this->di->setShared('config', function() use ($config_path) {
			return new NoodConfig($config_path);
		});
	}

	/**
	 * 获取服务
	 */
	public function get($service_name)
	{
		if( is_object( $service = $this->di->get($service_name) ) ) {
			return $service;
		} else {
			throw new \Exception("没有找到对应的服务");	
		}
	}

	/**
	 * 获取服务容器
	 */
	public function getDI()
	{
		if(is_object($this->di)) {
			return $this->di;
		} else {
			return false;
		}
	}

	/**
	 * 初始化单例db服务
	 */
	/*private function _initSharedDb()
	{
		$this->di->setShared('db', function(){
			$config_service = $this->get('config');
			$db_config_list = $config_service->get('database');
			$list = [
                "host"     => $db_config_list['host'],
                "username" => $db_config_list['username'],
                "password" => $db_config_list['password'],
                "dbname"   => $db_config_list['dbname']
			];
			return new PdoMysql($list);
		});
	}*/

	/**
	 * 初始化log日志服务
	 */
	protected function _initLog()
	{
		//日志还有待完善，主要是对monolog还不是很了解
		$this->di->set('log', function(){
			$default_channel_name = $this->get('config')->get('default_channel_name');
			$logger = new Logger($default_channel_name);
			$log_name = "{$default_channel_name}-" . date('Y-m-d') . ".log";
			$log_path = CLI_APP_ROOT . 'logs' . DIRECTORY_SEPARATOR . $log_name;
			$logger->pushHandler(new StreamHandler($log_path));
			return $logger;
		});
	}

	/**
	 * 初始化模拟请求服务
	 */
	protected function _initCurlRequest()
	{
		$this->di->set('curl_request', function(){
			return new 	CurlRequest();
		});
    }

    /**
     * 初始化redis服务
     */
    protected function _initRedis()
    {
        $this->di->set('redis',function(){
            $config_service = $this->get('config');
            $redis_config = $config_service->get('redis');
            $redis_service = new \Redis();
            $redis_service->connect($redis_config['host'], $redis_config['port'], $redis_config['timeout']);
            $redis_service->select(0);
            return $redis_service;
        });
    }

    /**
     * 初始化profiler服务
     */
    protected function _initProfiler()
    {
        $this->di->set('profiler', function(){
                return new \Phalcon\Db\Profiler();
        }, true);
    }

    /**
     *  初始化db服务
     */
    protected function _initDBWithProfiler($is_reconnected = false)
    {
        $this->di->setShared('db', function(){
            $config_service = $this->get('config');
            $db_config_list = $config_service->get('database');
            $list = [
                "host"     => $db_config_list['host'],
                "username" => $db_config_list['username'],
                "password" => $db_config_list['password'],
                "dbname"   => $db_config_list['dbname'],
                "options"  => $db_config_list['options']
            ];
            $connection = new PdoMysql($list);

            //新建事件管理器
            $eventsManager = new \Phalcon\Events\Manager();
            $profiler = $this->get('profiler');

            //监听所有的db事件
            $eventsManager->attach('db', function($event, $connection) use ($profiler) {
                //一条语句查询之前事件，profiler开始记录sql语句
                if ($event->getType() == 'beforeQuery') {
                    $profiler->startProfile($connection->getSQLStatement());
                }
                //一条语句查询结束，结束本次记录，记录结果会保存在profiler对象中
                if ($event->getType() == 'afterQuery') {
                    $profiler->stopProfile();
                }
            });

            //将connection绑定起来
            $connection->setEventsManager($eventsManager);

            return $connection;
		});
    }

    /**
     * 注入队列服务
     */
    protected function _initMq()
    {
        $this->di->set('mq',function(){
            return MqFactory::getMq('RabbitMq');
        });
    }

    /**
     * mysql gone away是进行重新连接
     */
    public function reConnectPDO()
    {
        $this->_initDBWithProfiler($is_reconnected = true);
    }
}
