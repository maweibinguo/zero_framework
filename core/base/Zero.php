<?php
namespace core\base;

/**
 * zero框架
 */
class Zero
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

        /* 合并配置文件 */
        if( FRAME_MODE == 'develop' ) {
            $service_config = SERVICE_APP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'develop' . DIRECTORY_SEPARATOR;
            $common_config = CONFIG_ROOT . 'develop' . DIRECTORY_SEPARATOR;
        } else {
            $service_config = SERVICE_APP_ROOT . 'config' . DIRECTORY_SEPARATOR . 'product' . DIRECTORY_SEPARATOR;
            $common_config = CONFIG_ROOT . 'product' . DIRECTORY_SEPARATOR;
        }

        /* 返回最终结果 */
        $config_path_list = [
            $common_config, $service_config
        ];
        return $config_path_list;
    }

}
