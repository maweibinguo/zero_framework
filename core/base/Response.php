<?php
namespace core\base;

use Phalcon\Http\Response as FrameResponse;

class Response extends FrameResponse
{
    /**
     * 响应成功,整个业务执行完成时使用
     *
     * @params string $message 提示信息
     * @params array $data_item 响应结果
     */
    public function success($message = '', $data = [])
    {
        $return_data = [
                            'status'    => 'success',
                            'data'      => $data,
                            'message'   => $message
                        ];    
        $this->json($return_data);
    }

    /**
     * 响应失败,业务执行失败时候使用
     *
     * @params string $message 提示信息
     * @params array $data_item 响应结果
     */
    public function error($message = '', $data = [])
    {
        $return_data = [
                            'status'    => 'failed',
                            'data'      => $data,
                            'message'   => $message
                        ];    
        $this->json($return_data);
    }

    /**
     * 响应的通用方法
     * 
     * @params array $data_item 响应结果
     */ 
    public function json($data = [])
    {
        $this->setJsonContent($data);
        $this->send();
        exit;
    }
}
