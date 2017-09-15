<?php
/**
 * 文章表单
 */
namespace app\admin\validator;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Callback;

class Picture
{
    /**
     * 获取添加文章是的验证器
     */
    public function getAddValidator() 
    {
        //文章标题
        $validation = new Validation();
        $validation->add('picturename', new PresenceOf([   'message' => '轮播图名称必填' ]));
        $validation->add('picturename', new StringLength([ 'max'     => 64, 
                                                     'message' => '轮播图名称的长度不得超过64个字符']));
        $validation->setFilters('picturename', 'trim');

        //文章标签
        $validation->add('targeturl', new PresenceOf(['message' => '请填写跳转链接']));
        $validation->setFilters('targeturl', 'trim');

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
                                                    'message' => '轮播图的状态不正确'
                                                ]));

        //轮播图
        $validation->add('headimage', new PresenceOf([   'message' => '请上传轮播图' ]));
        $validation->setFilters('headimage', 'trim');

        return $validation;
    }
}
