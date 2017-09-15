<?php
/**
 * 封装了文章的相关业务逻辑
 */
namespace app\blog\service;

use core\base\Components;
use app\blog\models\UserModel;

class AccService extends Components
{
    /**
     * 注册用户
     */
    public function register($user_data)
    {
        $user_model = new UserModel();
        $user_data['add_time'] = time();
        $user_model->addUser($user_data);
    }

    /**
     * 登录
     */
    public function login($user_name, $password)
    {
        $user_model = new UserModel();
        $user_data = $user_model->login($user_name, $password);
        $user_data_encrypt = $this->jwt->encrypt($user_data);
        $login_session_name = $this->config->get('login_session_name');
        $this->session->set($login_session_name, $user_data_encrypt);
    }

    /**
     * 统计用户的数量
     */
}
