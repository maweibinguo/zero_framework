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
        $builder = new CaptchaBuilder();
        $this->session->set('captcha', $builder->getPhrase());
        $builder->setBackgroundColor(255 , 255 , 255);
        $builder->build(130, 40, null);    
        $builder->output();
    }
}
