<?php
/**
 * 命令行入口文件
 * @author zero<maweibinguo@163.com>
 */
date_default_timezone_set("Asia/Shanghai");
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * 定义核心常量
 */
define('IS_DEBUG', 1);//框架的开发状态
define('DS', DIRECTORY_SEPARATOR);//目录分隔符名字太长了
defined( 'FRAMEWORK_ROOT' ) or define( 'FRAMEWORK_ROOT', __DIR__ . DS );//框架根路径
defined( 'CORE_COMPONTS' ) or define( 'CORE_COMPONTS', FRAMEWORK_ROOT . 'core' . DS );//核心组件路径
defined( 'VENDOR_COMPONTS' ) or define( 'VENDOR_COMPONTS', FRAMEWORK_ROOT . 'vendor' . DS );//第三方组件路径
defined( 'APP_ROOT' ) or define( 'APP_ROOT', __DIR__ . DS . 'app' .DS );// 应用根路径
defined( 'ATTACH_ROOT' ) or define( 'ATTACH_ROOT', __DIR__ . DS . 'attach' .DS );//附件的根路径
defined( 'CONFIG_ROOT' ) or define( 'CONFIG_ROOT', __DIR__ . DS . 'config' .DS );//配置文件的根路径

try{
        /**
        * 引入composer自动加载
        */
        require(VENDOR_COMPONTS . 'autoload.php');

        /**
         * 加载配置文件
         */
        $config_path_list = \core\base\Zero::getConfigFile();

        \core\base\Zero::$app = new \core\base\web\WebApp($config_path_list);
        \core\base\Zero::$app->run();
} catch(\Exception $e) {

}
