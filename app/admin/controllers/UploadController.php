<?php
namespace app\admin\controllers;

use core\base\Attach;

class UploadController extends BaseController
{
    /**
     * 上传图片
     */
    public function indexAction()
    {
        $attach = new Attach();

        /* 初始化返回结果 */
        $return_data = [    'success' => 0,
                            'message' => '',
                            'url'     => ''    ];

        /* 开始上传图片 */
        $config_service = $this->di->get('config');
        $upload_config = $config_service->get('upload_file');
        $max_file_size = $upload_config['allow_max_size'];
        $allow_type_list = $upload_config['allow_type'];
        if($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();    
            if(is_array($files) && count($files)) {
                foreach($files as $file) {
                    $type = strtolower($file->getType());
                    if(!in_array($type, $allow_type_list)) {
                        $return_data['message'] = '上传失败';
                        exit(json_encode($return_data)); 
                    }

                    $file_size = $file->getSize();
                    if($file_size > $max_file_size) {
                        $return_data['message'] = '超过最大上传尺寸，最大为2M';
                        exit(json_encode($return_data)); 
                    }

                    $file_path = $attach->getFilePath('admin');
                    $file_name = $attach->getNewFileName($file->getName());
                    $file_full_path = $file_path . $file_name;
                    if($file->moveTo($file_full_path) === true) {
                        $return_data['message'] = '上传成功';
                        $return_data['success'] = 1;
                        $return_data['url'] = str_replace(FRAMEWORK_ROOT , '/', $file_full_path);
                        exit(json_encode($return_data));
                    } else {
                        exit(json_encode($return_data));
                    }
                }
            }
        }
    }
}
