<?php
namespace core\base\web;

use core\base\BaseApp;
use core\base\EventDispatcher;
use Phalcon\Mvc\Application;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;
use Phalcon\Events\Manager as EventsManager;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Volt;
use core\base\EventView;

/**
 * web应用基础组件
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
            echo $application->handle()->getContent();
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
        $service_name = static::getServiceName();
        $this->di->set('view', function() use ($service_name){
            $view = new View();

            //设置模板的基准目录
            $dir_name = APP_ROOT . $service_name . DS . 'views' . DS. 'template' . DS;
            $view->setViewsDir($dir_name);

            //设置编译模板后的保存目录
            $compile_dir = APP_ROOT. $service_name. DS . 'runtime' . DS . 'view_compile' . DS;
            if(!is_dir($compile_dir)) {
                mkdir($compile_dir);
            }
            $view->registerEngines([
                '.volt' => function ($view, $di) use ($compile_dir) {
                    $volt = new Volt($view, $di);
                    $volt->setOptions([
                        'compileAlways'=>IS_DEBUG ? true : false,
                        'compiledPath' => $compile_dir,
                        'compiledSeparator' => '_'
                    ]);
                    return $volt;
                    return $volt;
                },
            ]);

            $events_manager = new EventsManager();
            $events_manager->attach("view",new EventView());
            $view->setEventsManager($events_manager);
            return $view;
        });
    }
}
