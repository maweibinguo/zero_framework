<?php
namespace core\base;

use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;

class EventDispatcher extends Components
{
    /**
     * 在进入循环调度前触发，此时触发器不知道要执行的控制器方法是否存在，
     * 调度器只知道路由传过来的信息
     */
    public function beforeDispatchLoop(Event $event, $dispatcher)
    {
        $service_name = static::getServiceName();
        $config_service = $this->di->get('config');
        $default_namespace = $config_service->get('default_namespace'); 
        $service_namespace = $default_namespace . $service_name . '\\controllers\\';
        $dispatcher->setDefaultNamespace($service_namespace);
        return true;
    }

    /**
     * 在调度器抛出异常时触发
     */
    public function beforeException(Event $event, Dispatcher $dispatcher, \Exception $exception)
    {
        $dispatcher->forward([
            'controller' => 'public',
            'action'     => 'error'
        ]); 
        $dispatcher->setParams([
            'code'    => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine() 
        ]);
        return true;
    }
}
