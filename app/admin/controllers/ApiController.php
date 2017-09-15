<?php
/**
 * 提供对外服务的接口
 */
namespace app\admin\controllers;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use app\admin\service\PictureService;

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

    /**
     * 获取轮播图列表
     */
    public function pictureAction()
    {
        $picture_service = new PictureService();
        $picture_id_list = $picture_service->getPictureList(['status' => 1]);
        if(!empty($picture_id_list))  {
            foreach($picture_id_list as $picture_id) {
                $picture_item = $picture_service->getPictureDetail($picture_id);
                $picture_list[] = $picture_item;
            }
        }
        exit(json_encode($picture_list));
    }
}
