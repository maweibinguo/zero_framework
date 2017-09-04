<?php
namespace app\blog\controllers;

use app\blog\service\AccService;
use app\blog\validator\User as UserValidator;

class AccController extends BaseController
{
    /**
     * 注册用户
     */
    public function registerAction()
    {
        try{
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
            $post_data = $this->request->get();
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
       $this->session->destroy();
       return $this->response->redirect('/index/index');
    }
}
