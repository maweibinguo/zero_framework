<?php
namespace app\blog\controllers;

use Phalcon\Mvc\View;

class ErrorController extends BaseController
{
    /**
     * 页面渲染禁用级别
     */
    public $disable_level = [
        View::LEVEL_AFTER_TEMPLATE,
                            ];

    /**
     * 404报错页面展示
     */
    public function notFoundAction()
    {
        array_push($this->common_css, 'css/error.css');
        $this->title = '错误页面';
    }
}
