<?php
namespace app\blog\controllers;

class UploadController extends BaseController
{
    /**
     * 上传图片
     */
    public function indexAction()
    {
        /* 初始化返回结果 */
        $return_data = [    'success' => 0,
                            'message' => '',
                            'url'     => ''    ];

        /* 开始上传图片 */
        if($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();    
            if(is_array($files) && count($files)) {

            } else {
                
            }
        } 
    }
}
