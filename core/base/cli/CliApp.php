<?php
namespace core\base\cli;
use Phalcon\Cli\Console as Console;
use Phalcon\Loader;
use core\base\BaseApp;

/**
 * 命令行基础组件
 */
class CliApp extends BaseApp
{
    /**
     * 返回当前应用的服务名称
     */
    public function getServiceName()
    {
        return $this->_service_name;
    }

	/**
	 * 启动框架
	 */
	public function run()
	{
        try{
            $this->_bindService();
			$this->_registerAutoloadNamespace();
			$this->_dispatch();
		} catch(\Exception $e) {
			echo "\n=========================================\n";
			echo $e->getMessage();	
			echo "\n=========================================\n";
		}		
    }

    /**
     * 绑定服务
     */
    private function _bindService()
    {
        //获取当前对象的方法
        $methods_list = get_class_methods($this);

        //开始调用绑定服务的方法
        foreach ($methods_list as $method) {
            if ((strlen($method) > 10) && (strpos($method, '_initShared') === 0)) {
                $this->$method();
                continue;
            }
            
            if ((strlen($method) > 4) && (strpos($method, '_init') === 0)) {
                $this->$method();
                continue;
            }
        }
    }

	/**
	 * 分发路由
	 */
	private function _dispatch()
	{
		$router = $this->di->get('router');
		$router_list = $router->getRouterParams();
		$console = new Console($this->di);
		$console->handle($router_list);
	}

	/**
	 * 注册哪些命名空间下的文件自动加载
	 */
	private function _registerAutoloadNamespace()
	{
		$config_service = $this->di->get('config');
		$autoload_namespace_list = $config_service->get('autoload_namespace');
		if(is_array($autoload_namespace_list) && 
												!empty($autoload_namespace_list)) {
			$loader = new Loader();		
			$loader->registerNamespaces($autoload_namespace_list);
			$loader->register();
		}
	}

	/**
	 * 注册路由服务
	 */
	protected function _initRouter()
	{
		//日志还有待完善，主要是对monolog还不是很了解
		$this->di->set('router', function(){
			return $router = new CliRouter();
		});
	}
}
