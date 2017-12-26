<?php
namespace core\base;

/**
 * zero框架
 */
class Zero extends Components
{
    /**
     * 框架的应用
     */
    public static $app;

    /**
     * 获取配置文件
     */
    public static function getConfigFile()
    {
        /* 初始化返回结果 */
        $config_path_list = [];
        $service_name = static::getServiceName();
        $service_app_root = APP_ROOT . $service_name . DS;

        /* 合并配置文件 */
        if( IS_DEBUG ) {
            $service_config = $service_app_root . 'config' . DS . 'develop' . DS;
            $common_config = CONFIG_ROOT . 'develop' . DS;
        } else {
            $service_config = $service_app_root. 'config' . DS . 'product' . DS;
            $common_config = CONFIG_ROOT . 'product' . DS;
        }

        /* 返回最终结果 */
        $config_path_list = [
            $common_config, $service_config
        ];
        return $config_path_list;
    }

    /**
     * 获取zero框架的版本号
     */
    public function getVersion()
    {
        return '1.0';
    }

}
