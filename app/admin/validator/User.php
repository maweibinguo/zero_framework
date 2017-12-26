<?php
/**
 * 文章表单
 */
namespace app\admin\validator;

use core\base\Components;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;

class User extends Components
{
    /**
     * 获取添加文章是的验证器
     */
    public function getAddValidator() 
    {
        //用户名称
        $validation = new Validation();
        $validation->add('user_name', new PresenceOf([   'message' => '用户名必填' ]));
        $validation->setFilters('title', 'trim');

        //用户密码
        $validation->add('password', new PresenceOf(['message' => '用户密码必填']));
    
        //验证码
        $validation->add('captcha', new Callback([  'callback' => function($data) {
                                                                                        $captcha = $this->session->get('captcha');
                                                                                        if($data['captcha'] == $captcha) {
                                                                                            return true;
                                                                                        } else {
                                                                                            return false;
                                                                                        }
                                                                                    },
                                                    'message'  => '验证码不正确'  ]));        
        return $validation;
    }
}
