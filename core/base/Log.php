<?php
/**
 * 日志组件，基于Monolog
 */
namespace core\base;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log extends Components
{
    /**
     * 日志服务
     */
    private static $logger_list = [];

    /**
     * 获取日志实例
     */
    public static function getInstance($channel= '')
    {
        $channel = !empty($channel) ? $channel : 'common';

        if(isset(static::$logger_list[$channel]) && is_object(static::$logger_list[$channel])) {
            return static::$logger_list[$channel];
        } else {
            //日志实例
            $logger = new Logger($channel);

            //创建处理的handler
            $sub_dir = date('Y-m');
            $save_dir = APP_ROOT . static::getServiceName() . DS . 'runtime' . DS . 'logs' . DS . $sub_dir . DS;
            $log_name = "common-" . date('Y-m-d') . ".log";
            $log_path = $save_dir . $log_name;
            $logger->pushHandler(new StreamHandler($log_path));
            static::$logger_list[$channel] = $logger;
            return static::$logger_list[$channel];
        }
    }
}
