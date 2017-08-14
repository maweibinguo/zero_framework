<?php
namespace app\blog\controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use core\base\Zero;

class BaseController extends Controller
{
    /**
     * 关键字
     * @var string
     */
    public $keywords = 'insisting,php技术博客';

    /**
     * 描述
     * @var string
     */
    public $description = '一个php程序员的技术分享博客。';

    /**
     * 标题
     * @var string
     */
    public $title = 'insisting的技术博客';

    /**
     * 禁用模板渲染的级别
     */
    public $disable_level = [];

    /**
     * 公共的js
     */
    public $common_js = [
                            'js/jquery.min.js',
                            'js/nprogress.js',
                            'js/jquery.lazyload.min.js',
                            'js/bootstrap.min.js',
                            'js/jquery.ias.js',
                            'js/scripts.js'
                        ];

    /**
     * 公共的css
     */
    public $common_css = [
                            'css/bootstrap.min.css',
                            'css/nprogress.css',
                            'css/style.css',
                            'css/font-awesome.min.css'
                         ];

    /**
     * 页面的js
     */
    public $page_js = [];

    /**
     * 页面css
     */
    public $page_css = [];

    /**
     * 在路由后执行的方法
     */
    public function afterExecuteRoute()
    {
        $this->initHeader();
        $this->registerCommonJsCss();
        $this->registerPageJsCss();
    }

    /**
     * 初始化头部信息
     */   
    public function initHeader()
    {
        $this->view->setVar('keywords', $this->keywords);
        $this->view->setVar('description', $this->description);
        $this->view->setVar('title', $this->title);
        $this->view->disableLevel($this->disable_level);
    }

    /**
     * 初始化公共的样式
     */
    public function registerCommonJsCss()
    {
        //初始化资源集合
        $footer_collect = $this->assets->collection('footer'); 
        $header_collect  = $this->assets->collection('header'); 

        //头部css
        if($this->common_css) {
            foreach($this->common_css as $path) {
                $header_collect->addCss($path);
            }
        }

        //底部js
        if($this->common_js) {
            foreach($this->common_js as $path) {
                $footer_collect->addJs($path); 
            }
        }
    }

    /**
     * 初始化页面的样式
     */
    public function registerPageJsCss()
    {
        //初始化资源集合
        $footer_collect = $this->assets->collection('footer'); 
        $header_collect  = $this->assets->collection('header'); 

        //头部css
        if($this->page_css) {
            foreach($this->page_css as $path) {
                $header_collect->addCss($path);
            }
        }

        //底部js
        if($this->page_js) {
            foreach($this->page_js as $path) {
                $footer_collect->addJs($path); 
            }
        }
    }

    /**
     * 
     */
}
