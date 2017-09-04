<?php
namespace core\base;

use Phalcon\Mvc\User\Component;

/**
 * 所有核心组建的基础组建
 */
class Components extends Component
{
    /**
     * 获取服务名称
     */
    public static function getServiceName()
    {
        if( isset($_SERVER['SERVICE_NAME_APP']) ) {
            $service_name = strtolower($_SERVER['SERVICE_NAME_APP']);
            return $service_name;
        } else {
            //获取域名@todo
           $request = $this->di->get('request'); 
        }
    }

    /**
     * 获取错误信息
     */
    public function getErrorMessage($error_message, $class_name, $method_name, $lines)
    {
        $message_list = [    $error_message,
                             $class_name,
                             $method_name,
                             $lines    ]; 
        $message_str = implode(" >> ", $message_list);
        return $message_str;
    }
}
