<?php
/**
 * 文章表单
 */
namespace app\blog\validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;

class Article
{
    /**
     * 获取添加文章是的验证器
     */
    public function getAddValidator() 
    {
        //文章标题
        $validation = new Validation();

        //文章markdown内容
        $validation->add('mdcontent', new PresenceOf([   'message' => '文章内容必填' ]));
        $validation->setFilters('mdcontent', 'trim');

        //文章html内容
        $validation->add('htmlcontent', new PresenceOf([   'message' => '文章内容必填' ]));
        $validation->setFilters('htmlcontent', 'trim');
        
        return $validation;
    }
}
