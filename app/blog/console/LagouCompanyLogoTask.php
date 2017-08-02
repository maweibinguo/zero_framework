<?php
namespace Console\Controllers;
use Phalcon\Cli\Task;
use Core\Zero;
use App\Company\Models\CompanyExtendModel;
use Console\Service\CompanyService;

class LagouCompanyLogoTask extends Task
{
    use \Core\SaveRemoteFile;

    /**
     * 保存附件
     */
    public function testAction()
    {
        $mq = Zero::$app->service->get('mq');
        $call_back  = [$this, 'callBack'];
        $mq->pull($call_back, 'lagou_company_log');
    }

    /**
     * 处理logo保存的业务逻辑
     */
    private function _saveComapnyLogo($message)
    {
        /* 初始化消费结果 */
        $consume_result = false;

        /* 开始消费 */
        $message_body_list = json_decode($message->getBody(), true);
        if(!is_array($message_body_list) || empty($message_body_list)) {
            return $consume_result;
        }
       
        /* 开始采集 */ 
        foreach($message_body_list as $message_item) {
            if( empty($message_item['company_logo']) ) {
                return $consume_result;
            }
            $message_item['company_logo'] = 'https:' . $message_item['company_logo'];
            $file_path = $this->getRemoteFile($message_item['company_logo'], 'company_logo', '', false);
            $model = new CompanyExtendModel();
            $company_extend_obj = $model->getDetailByExtendCompanyID($message_item['company_extend_id']);
            if($company_extend_obj === false) {
                //@todo这种情况不打可能出现
                return true;
            }
            $property_value_item = json_decode($company_extend_obj->PropertyValue, true);
            $property_value_item['CompanyLogo'] = $file_path;
            $company_extend_obj->PropertyValue = json_encode($property_value_item);
            $result = $company_extend_obj->save();
            if($result !== true) {
                return $consume_result;
            }
        } 
       
        $log_service = Zero::$app->service->get('log'); 
        $log_service = $log_service->withName(CompanyService::COMPANY_SERVICE_LOG);
        $min_id = $message_body_list[0]['company_extend_id'];
        $last_item = end($message_body_list);
        $max_id = $last_item['company_extend_id'];
		$log_service->addInfo("company_extend_id : {$min_id} - {$max_id}的logo 信息采集成功");
    
        usleep(2);
        $consume_result = true;
        return $consume_result; 
    }

    /**
     * 队列的回调地址
     */
    public function callBack($message, $mq)
    {
        try {
            $result = $this->_saveComapnyLogo($message); 
            return $result;
        } catch (\Exception $e) {
            $error_message = strtolower('MySQL server has gone away');
            if(strpos(strtolower($e->getMessage()), $error_message) !== false) {
                Zero::$app->service->reConnectPDO();
                echo "====\n";
            }
        }
    }
}

