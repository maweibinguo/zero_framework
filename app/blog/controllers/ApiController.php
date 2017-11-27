<?php
/**
 * 提供对外服务的接口
 */
namespace app\blog\controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;

class ApiController extends BaseController
{
    public function captchaAction()
    {
        header('Content-type: image/jpeg');
        $code_item = array_merge(range(0,9), range('a', 'z'));
        shuffle($code_item);
        $code = implode('', array_slice($code_item, 0, 4));
        $builder = new CaptchaBuilder($code);
        $this->session->set('captcha', $builder->getPhrase());
        $builder->setBackgroundColor(255 , 255 , 255);
        $builder->build(135, 45, null);    
        $builder->output();
    }
}
