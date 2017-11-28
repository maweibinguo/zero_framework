<?php
/**
 * 文章表单
 */
namespace app\blog\validator;

use core\base\Components;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;

class Comment extends Components
{
    /**
     * 获取添加文章是的验证器
     */
    public function getAddValidator() 
    {
        //评论内容
        $validation = new Validation();
        $validation->add('article_comment', new PresenceOf([   'message' => '评论内容为空' ]));
        $validation->setFilters('title', 'trim');

        //文章编号
        $validation->add('article_id', new PresenceOf(['message' => '评论的文章编号为空']));

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
