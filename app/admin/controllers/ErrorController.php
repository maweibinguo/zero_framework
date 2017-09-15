<?php
namespace app\admin\controllers;

use Phalcon\Mvc\View;

class ErrorController extends BaseController
{
    /**
     * 页面渲染禁用级别
     */
    public $disable_level = [
                                View::LEVEL_BEFORE_TEMPLATE => true,
                                View::LEVEL_AFTER_TEMPLATE => true,
                                View::LEVEL_LAYOUT => true,
                                View::LEVEL_AFTER_TEMPLATE => true,
                                View::LEVEL_MAIN_LAYOUT => true
                            ];
    /**
     * 页面样式
     */
    public $page_css = [
                            '/css/error.css'
                       ];

    /**
     * 404报错页面展示
     */
    public function notFoundAction()
    {
        $this->title = '错误页面';
    }
}
