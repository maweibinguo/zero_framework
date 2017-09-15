<?php
namespace app\admin\controllers;

use app\admin\service\PictureService;
use app\admin\validator\Picture as PictureValidator;
use app\admin\models\MottoModel;
use core\base\Log;

class PictureController extends BaseController
{
    /**
     * 页面样式
     */
    public $page_css = [    '/css/checkbox3.min.css'    ];

    /**
     * 页面js
     */
    public $page_js = [    '/js/layer/layer/layer.js',
                           '/js/ajaxfileupload.js',
                           '/js/upload.js',
                           '/js/picture.js'    ];

    /**
     * 轮播图列表
     */
    public function indexAction()
    {
        try{
            $picture_status_list = $this->config->get('picture_status_list');              
            $picture_service = new PictureService();
            $picture_id_list = $picture_service->getPictureList([]);
            if(!is_array($picture_id_list)) {
                $picture_id_list = [];
            }
            $picture_list = [];
            foreach($picture_id_list as $picture_id) {
                $picture_item = $picture_service->getPictureDetail($picture_id);   
                $picture_list[] = $picture_item;
            }
            $this->view->setVar('picture_list', $picture_list);
            $this->view->setVar('picture_status_list', $picture_status_list);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error("页面加载失败"); 
            return $this->response->redirect('error/notFound');
        }
    }
    
    /**
     * 添加轮播图
     */
    public function addAction()
    {
        try {
            if($this->request->isPost()) {
                $post_data = $this->request->getPost(); 
                $picture_validator = new PictureValidator();
                $add_picture_validator = $picture_validator->getAddValidator();
                $messages_list = $add_picture_validator->validate($post_data);
                if(count($messages_list)) {
                    foreach($messages_list as $message_item) {
                        $message = $message_item->getMessage();
                        $error_message = Log::getErrorMessage($message, __CLASS__, __METHOD__, __LINE__);
                        throw new \Exception($error_message);
                    }
                }
                
                /* 开始添加轮播图 */
                $picture_service = new PictureService(); 
                $post_data['add_time'] = time();
                $picture_service->addPicture($post_data);
                $this->responseSuccess('添加轮播图成功');
            } else {
                $this->title='添加轮播图';
            }
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed("添加轮播图失败");
        }
    }

    /**
     * 修改轮播图
     */
    public function modifyAction()
    {
        try {
            if($this->request->isPost()) {
                $post_data = $this->request->getPost();
                $picture_service = new PictureService();
                $picture_service->modifyPicture($post_data);
                $this->flashSession->success("修改成功"); 
                $this->responseSuccess('修改轮播图成功');
            } else {
                $picture_id = $this->request->get('picture_id');
                $picture_service = new PictureService();
                $picture_detail = $picture_service->getPictureDetail($picture_id);
                $picture_status_list = $this->config->get('picture_status_list');
                $this->view->setVar('picture_detail', $picture_detail);
                $this->view->setVar('picture_id', $picture_id);
                $this->view->setVar('picture_status_list', $picture_status_list);
                $this->view->pick('picture/add');
            }
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed("添加轮播图失败");
        }
    }
}
