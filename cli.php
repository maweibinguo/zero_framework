<?php
/**
 * 命令行入口文件
 * @author zero<maweibinguo@163.com>
 */
date_default_timezone_set("Asia/Shanghai");

/**
 * 定义框架的开发模式
 */
define('FRAME_MODE', 'develop');

switch(FRAME_MODE) {
    case 'develop':
        ini_set('display_errors', 1);
        error_reporting(E_ERROR | E_WARNING | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR);
        break;
    case 'product':
        ini_set('display_errors', 0);
        break;
}

/**
 * 定义核心常量
 */
defined( 'FRAMEWORK_ROOT' ) or define( 'FRAMEWORK_ROOT', __DIR__ . DIRECTORY_SEPARATOR );//框架根路径
defined( 'CORE_COMPONTS' ) or define( 'CORE_COMPONTS', FRAMEWORK_ROOT . 'core' . DIRECTORY_SEPARATOR );//核心组件路径
defined( 'VENDOR_COMPONTS' ) or define( 'VENDOR_COMPONTS', FRAMEWORK_ROOT . 'vendor' . DIRECTORY_SEPARATOR );//第三方组件路径
defined( 'APP_ROOT' ) or define( 'APP_ROOT', __DIR__ . DIRECTORY_SEPARATOR . 'app' .DIRECTORY_SEPARATOR );// 应用根路径
defined( 'ATTACH_ROOT' ) or define( 'ATTACH_ROOT', __DIR__ . DIRECTORY_SEPARATOR . 'attach' .DIRECTORY_SEPARATOR );//附件的根路径
defined( 'CONFIG_ROOT' ) or define( 'CONFIG_ROOT', __DIR__ . DIRECTORY_SEPARATOR . 'config' .DIRECTORY_SEPARATOR );//配置文件的根路径

$service_name = $argv[1];
if( !isset($service_name) ) {
    echo "\r\n=================================================================\r\n";
    echo "\r\n命令行用法说明: phppath [path]/cli.php service controllername methodname params1 params2 ...\r\n";
    echo "\r\n=================================================================\r\n";
    exit();
} else {
    defined( 'SERVICE_APP_ROOT' ) or define( 'SERVICE_APP_ROOT', APP_ROOT . strtolower($service_name) . DIRECTORY_SEPARATOR  );//设置当前子系统的根目录
}

/**
 * 引入composer自动加载
 */
require(VENDOR_COMPONTS . 'autoload.php');

/**
 * 加载配置
 */
$config_path_list = \core\base\Zero::getConfigFile();

/**
 * 启动框架
 * 1、注册框架所需核心服务
 * 2、完成路由
 */
\core\base\Zero::$app = new \core\base\cli\CliApp($service_name, $config_path_list);
\core\base\Zero::$app->run();
