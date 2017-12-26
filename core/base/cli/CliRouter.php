<?php
namespace core\base\cli;
use Phalcon\Cli\Router;

/**
 * 框架核心路由类
 */
class CliRouter extends Router
{
	private $_router_list;

	/**
	 * 获取路由参数
	 */
	public function getRouterParams()
	{
		/* 初始化返回结果 */
		$this->_router_list = [];

		/* 获取路由 */
		global $argv;
        foreach($argv as $offset => $arg_value) {
            if($offset == 1) {
                $this->_router_list['service'] = $arg_value;
            } elseif ($offset == 2) {
				$this->_router_list['task'] = $arg_value;
			} elseif($offset == 3) {
				$this->_router_list['action'] = $arg_value;
			} elseif($offset >= 4) {
				$this->_router_list['params'][] = $arg_value;
			}
        }

		if(empty($this->_router_list['service'])) {
			throw new \Exception("服务名称为空");
		}
		if(empty($this->_router_list['task'])) {
			throw new \Exception("控制器名称为空");
		}
		if(empty($this->_router_list['action'])) {
			throw new \Exception("方法名称为空");
		}

		/* 返回最终结果 */
		return $this->_router_list;
	}	

	/**
	 * 处理路由,看读懂原来的框架是怎么写的，暂时先exit吧@todo
	 */
	public function handle($router_list)
	{
        $service_name = strtolower($router_list['service']);
		$task_name = ucfirst($router_list['task']) . 'Task';
        $action_name = $router_list['action'];
        $params_list = isset($router_list['params']) && !empty($router_list['params']) ? $router_list['params'] : null;

		$class_name = "app\\{$service_name}\\console\\$task_name";
		if(!class_exists($class_name)) {
			throw new \Exception("找不到类{$class_name}");
		}
		$controller_reflection = new \ReflectionClass($class_name);
		$method_name = trim(strtolower($router_list['action'])) . 'Action';
		if(! $controller_reflection->hasMethod($method_name)) {
			throw new \Exception("{$class_name}中并不存在方法{$method_name}");
		}
		$method_reflection = $controller_reflection->getMethod($method_name);
		$params = isset($router_list['params']) ? $router_list['params'] : [];
		if(empty($params)) {
			$method_reflection->invoke(new $class_name());
		} else {
			$method_reflection->invokeArgs(new $class_name(), $params);
		}
		exit();
	}
}
