<?php
namespace core\base;
use Phalcon\Mvc\User\Component;
use Phalcon\DiInterface;
use Phalcon\Di\FactoryDefault;

/**
 * 所有核心组建的基础组建
 */
class Components extends Component
{

	/**
	 * 当方法不存在时调用
	 */
	public function __call($method_name, $arguments)
	{
		if( method_exists($this, $method_name) ) {
			$this->$method_name($arguments);
		} else {
			throw new \Exception("__call未找到要调用的方法{$method_name}");
		}
    }

    /**
     * 获取制定类中的方法（不包含继承的）
     */
    public function getClassMethodList($class_name)
    {
        /* 初始化返回结果 */
        $methods_list = [];

        /* 获取指定类的方法 */
        $class_reflection = new \ReflectionClass($class_name);
        $methods_list_all = $class_reflection->getMethods();
        if($methods_list_all) {
            foreach($methods_list_all as $method_reflection) {
                if($method_reflection->class == $class_name) {
                    $methods_list[] = $method_reflection;
                }
            }
        }

        /* 返回最终结果 */
        return $methods_list;
    }
}
