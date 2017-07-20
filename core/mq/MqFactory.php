<?php
namespace core\mq;

/**
 * 消息队列工厂
 */
class MqFactory
{
    public static function getMq($type)
    {
        switch(strtolower($type)) {
            case 'rabbitmq':
                $class = "\\" . __NAMESPACE__ . '\\' . $type;
                if (!class_exists($class)) {
                    throw new \Exception('不支持此方式访问消息队列系统');
                }
                return new $class();
                break;
        }
    }

}
