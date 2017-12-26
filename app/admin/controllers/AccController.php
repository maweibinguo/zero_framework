<?php
namespace app\admin\controllers;

use app\admin\service\AccService;
use app\admin\validator\User as UserValidator;
use Phalcon\Mvc\View;

class AccController extends BaseController
{
    /**
     * 页面禁用级别
     */
    public $disable_level = [
                                View::LEVEL_BEFORE_TEMPLATE => true,
                                View::LEVEL_LAYOUT => true,
                                View::LEVEL_AFTER_TEMPLATE => true,
                                View::LEVEL_MAIN_LAYOUT => true
                            ];

    /**
     * 页面js
     */
    public $page_js = [
                        '/js/validate/user.js',
                        '/js/layer/layer/layer.js'
                      ];
    /**
     * 注册用户
     */
    public function registerAction()
    {
        try{
            
            if($this->request->isPost()) {
                $post_data = $this->request->getPost();
                $user_validator = new UserValidator();
                $add_user_validator = $user_validator->getAddValidator();
                $messages_list = $add_user_validator->validate($post_data);
                if(count($messages_list)) {
                    foreach($messages_list as $message_item) {
                        $error_message = $message_item->getMessage();
                        throw new \Exception($error_message);
                    }
                }
                $acc_service = new AccService();
                $acc_service->register($post_data);
                $this->responseSuccess('注册成功，请前去登录......');
           } else {
           
           }
        } catch (\Exception $e) {
            $error_message = $e->getMessage(); 
            $this->responseFailed($error_message);
        }
    }

    /**
     * 登录
     */
    public function loginAction()
    {
        try{
            if($this->request->isPost()) {
                $post_data = $this->request->getPost();
                $user_validator = new UserValidator();
                $add_user_validator = $user_validator->getAddValidator();
                $messages_list = $add_user_validator->validate($post_data);
                if(count($messages_list)) {
                    foreach($messages_list as $message_item) {
                        $error_message = $message_item->getMessage();
                        throw new \Exception($error_message);
                    }
                }
                $acc_service = new AccService();
                $acc_service->login($post_data['user_name'], $post_data['password']);
                $this->responseSuccess('登录成功，页面跳转中......');
            } else {
                $this->title = '用户登录页';
            }
        } catch(\Exception $e) {
            $error_message = $e->getMessage(); 
            $this->responseFailed($error_message);
        }
    }

    /**
     * 退出登录
     */
    public function logoutAction()
    {
       $remove_name = $this->config->get('admin_login_session_name');
       $this->session->remove($remove_name);
       return $this->response->redirect('/acc/login');
    }
}
