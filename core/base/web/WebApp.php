<?php
namespace core\base\web;
use core\base\BaseApp;
use core\base\EventDispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\View;

/**
 * 命令行基础组件
 */
class WebApp extends BaseApp
{
	/**
	 * 启动框架
	 */
	public function run()
	{
        try{
            $application = new Application($this->di);
            $application->handle()->getContent();
		} catch(\Exception $e) {
			echo "\n=========================================\n";
			echo $e->getMessage();	
			echo "\n=========================================\n";
		}		
    }

    /**
     * 设置项目调度
     */
    protected function _initDispatcher()
    {
        $this->di->set('dispatcher', function(){
            $events_manager = new EventsManager(); 
            $events_manager->attach('dispatch', new EventDispatcher());
            $dispatcher = new MvcDispatcher();
            $dispatcher->setEventsManager($events_manager);
            return $dispatcher;
        });
    }

    /**
     * 设置项目模板
     */
    protected function _initView()
    {
        $this->di->set('view', function(){
            $view = new View();
            return $view;
        });
    }
}
