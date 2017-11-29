<?php
namespace app\blog\controllers;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\View;
use core\base\Zero;
use core\base\ReSubmition;

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
                            'js/validate/user.js',
                            'js/layer/layer/layer.js',
                            'js/scripts.js',
                        ];

    /**
     * 公共的css
     */
    public $common_css = [
                            'css/bootstrap.min.css',
                            'css/nprogress.css',
                            'css/font-awesome.min.css',
                            'css/style.css',
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
     * 是否登录
     */
    public $is_login = false;

    /**
     * 在路由前执行的方法
     */
    public function beforeExecuteRoute($dispatcher)
    {
        //校验登录
        $controller_name = $dispatcher->getControllerName();
        $action_name = $dispatcher->getActionName();
        $access_controll_list = $this->config->get('needed_login');
        $login_data = $this->isLogin();
        if($login_data != false) {
            $this->is_login = true;
        }
        if(isset($access_controll_list[strtolower($controller_name)]) && 
                                                            in_array(strtolower($action_name), $access_controll_list[$controller_name])) {
            if($login_data === false ) {
                if($this->request->isAjax()) {
                    $this->responseFailed('请先登录!'); 
                } else {
                    header('Location:/index/index');
                    return false;
                    //return $this->response->redirect('index/index');
                }
            }
        }

        //校验是否是重复提交
        if($this->request->isPost()) {
            $need_check_list = $this->config->get('check_resubmit');            
            if(isset($need_check_list[$controller_name]) && 
                                                         in_array($action_name, $need_check_list[$controller_name])) {
                $post_data = $this->request->getPost();
                $name = $this->resubmition->getName();
                $unique_value = $post_data[$name];
                $check_result = $this->resubmition->isReSumit($unique_value);
                if($check_result === false) {
                    if($this->request->isAjax()) {
                        $this->responseFailed('请不要重复提交');
                    } else {
                        $this->flashSession->error('请不要重复提交'); 
                        return $this->response->redirect('error/notFound');
                    }
                
                }
            }
        }

        //校验令牌
        if($this->request->isPost() && !$this->security->checkToken()) {
            if($this->request->isAjax()) {
                $this->responseFailed('非法请求');
            } else {
                $this->flashSession->error('非法请求'); 
                return $this->response->redirect('error/notFound');
            }
        }
    }

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
        $user_data = $this->isLogin();
        if(!empty($user_data)) {
            $this->view->setVar('user_data', $user_data);
            $this->view->setVar('is_login', true);
        } else {
            $this->view->setVar('is_login', false);
        }

        $category_list = $this->config->get('category_list');
        $this->view->setVar('category_list', $category_list);
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
     * ajax成功响应
     */
    public function responseSuccess($message = '', $data = [], $is_check = true)
    {
        if($is_check === true) {
            $data['token_name'] = $this->security->getTokenKey();
            $data['token_value'] = $this->security->getToken();
            $data['resubmit_name'] = $this->resubmition->getName();
            $data['resubmit_value'] = $this->resubmition->getUniqueValue();
        }
        $return_data = [
                            'status'  => 'success',
                            'message' => $message,
                            'data'    => $data
                        ];
        exit(json_encode($return_data));
    }

    /**
     * ajax失败相应
     */
    public function responseFailed($message = '', $data = [], $is_check = true)
    {
        if($is_check === true) {
            $data['token_name'] = $this->security->getTokenKey();
            $data['token_value'] = $this->security->getToken();
            $data['resubmit_name'] = $this->resubmition->getName();
            $data['resubmit_value'] = $this->resubmition->getUniqueValue();
        }
        $return_data = [
                            'status'  => 'failed',
                            'message' => $message,
                            'data'    => $data
                        ];
        exit(json_encode($return_data));
    }

    /**
     * 校验是否登录
     */
    public function isLogin()
    {
        $result = false; 
        $login_session_name = $this->config->get('login_session_name');
        $login_session_data = $this->session->get($login_session_name);
        if(empty($login_session_data)) {
            return $result;
        }
        $result = (array)$this->jwt->decrypt($login_session_data);
        return $result;
    }

    /**
     * 获取是周几
     */
    public function getWeekDay($timestamp = null)
    {
        if(!isset($timestamp)) $timestamp = time();
        $week_day = date('w', $timestamp);
        $week_list =array("星期日","星期一","星期二","星期三","星期四","星期五","星期六");  
        return $week_list[$week_day];
    }
}
