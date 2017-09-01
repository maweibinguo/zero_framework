<?php
/**
 * 文章表单
 */
namespace app\blog\validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;

class User
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

        //令牌
        //$validation->add($this->security->getTokenKey());
        return $validation;
    }
}
