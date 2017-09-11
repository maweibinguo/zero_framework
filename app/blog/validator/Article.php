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
        $validation->add('title', new PresenceOf([   'message' => '文章标题必填' ]));
        $validation->add('title', new StringLength([ 'max'     => 64, 
                                                     'message' => '标题的长度不得超过64个字符']));
        $validation->setFilters('title', 'trim');

        //文章标签
        $validation->add('tag', new PresenceOf(['message' => '文章标签必填，请使用逗号隔开']));
        $validation->setFilters('tag', 'trim');

        //文章状态
        $validation->add('status', new Callback([
                                                    'callback' => function($data) {
                                                        $domain_item = [0, 1];
                                                        if(isset($data['status']) && in_array($data['status'], $domain_item)){
                                                            return true;
                                                        } else {
                                                            return false;
                                                        }
                                                    },
                                                    'message' => '文章的状态不正确'
                                                ]));

        //文章markdown内容
        $validation->add('mdcontent', new PresenceOf([   'message' => '文章内容必填' ]));
        $validation->setFilters('mdcontent', 'trim');

        //文章html内容
        $validation->add('htmlcontent', new PresenceOf([   'message' => '文章内容必填' ]));
        $validation->setFilters('htmlcontent', 'trim');
        
        //文章类别
        $validation->add('category', new PresenceOf([   'message' => '请选择文章类别' ]));
        $validation->setFilters('category', 'trim');

        //文章头图
        $validation->add('headimage', new PresenceOf([   'message' => '请上传文章头图' ]));
        $validation->setFilters('headimage', 'trim');

        return $validation;
    }
}
