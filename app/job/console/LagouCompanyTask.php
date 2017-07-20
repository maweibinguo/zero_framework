<?php
namespace Console\Controllers;
use Phalcon\Cli\Task;
use Core\Zero;
use Console\Service\CompanyService;
use App\Company\Models\CompanyModel;
use App\Company\Models\CityModel;

class LagouCompanyTask extends Task
{
	const PAGE_SIZE = 16;

    public function testAction()
    {
        $result = CityModel::find();

//获取所有的prifler记录结果，这是一个数组，每条记录对应一个sql语句
$profiles = Zero::$app->service->get('profiler')->getProfiles();
//遍历输出
foreach ($profiles as $profile) {
   echo "SQL语句: ", $profile->getSQLStatement(), "\n";
   echo "开始时间: ", $profile->getInitialTime(), "\n";
   echo "结束时间: ", $profile->getFinalTime(), "\n";
   echo "消耗时间: ", $profile->getTotalElapsedSeconds(), "\n";
}
        exit();
    }

	/**
	 * 采集公司信息
	 */
	public function getCompanyAction()
    {
        /* 初始化相关服务 */
		$request_service = Zero::$app->service->get('curl_request');
        $company_service = new CompanyService();
        $log_service = Zero::$app->service->get('log'); 
        $log_service = $log_service->withName(CompanyService::COMPANY_SERVICE_LOG);
		$config_service = Zero::$app->service->get('config');
        $redis = Zero::$app->service->get('redis');
        $mq = Zero::$app->service->get('mq');

        /* 判断是第几页 */
        $pre_key = $config_service->get('pre_key');
        $page_id_key = $pre_key . 'company:pageid';
        $page_id = $redis->get($page_id_key);
        $page_id = $page_id === false ?  0 : $page_id;

        /* 设置请求的参数 */
		$request_config = [
			'dest'     => 'https://www.lagou.com/gongsi/',
			'postdata' => [
							'first'     => false,
							'pn'        => $page_id + 1,
							] 
                        ];

		/* 匹配最基本的内容,一个li标签就是一个company */
		$content = $request_service->getData($request_config);
		$reg_exp_ul = <<<STR
					#<ul\s+class=['"]+\s*item_con_list\s*['"]\s*>(.*)</ul>#sU
STR;
		preg_match_all($reg_exp_ul, $content['data'], $matches_ul);
		$ul_content = isset($matches_ul[1][0]) ? trim($matches_ul[1][0]) : '';
		if(empty($ul_content)) {
			$log_service->addInfo("进行公司信息采集时没有发现相应的信息");
			return false;
		} else {

            /* 解析页面的采集数据 */

            //公司名称
            $company_name_list = $this->_getCompanyName($ul_content);

            //公司链接
            $company_url_list = $this->_getCompanyUrl($ul_content);

            //公司介绍
            $company_introduce_list = $this->_getCompanyIntroduce($ul_content);

            //公司logo
            $company_logo_list = $this->_getCompanyLogo($ul_content);

            //公司领域
            $company_web_list = $this->_getCompanyWeb($ul_content);

            //公司地址
            $company_place_list = $this->_getCompanyPlace($ul_content);
        
            //公司融资状况
            $company_finance_list = $this->_getCompanyFinance($ul_content);

			for($offset = 0; $offset < count($company_name_list); $offset++) {
                $channel_list = $config_service->get('channel_list');
                $channel_id = $channel_list['lagou'];
                $company_list[$offset]['channel_id'] = $channel_id;//采集渠道

                $company_list[$offset]['company_name'] = $company_name_list[$offset];//公司名称

                $company_list[$offset]['company_url'] = $company_url_list[$offset];//公司链接

				$company_list[$offset]['company_introduce'] = $company_introduce_list[$offset];//公司介绍
                $company_list[$offset]['company_logo'] = $company_logo_list[$offset];//公司logo

                $company_list[$offset]['company_web'] = $company_web_list[$offset];//公司领域

                $company_list[$offset]['company_place'] = $company_place_list[$offset];//公司地址

				$company_list[$offset]['company_finance'] = $company_finance_list[$offset];//公司融资状况
			}

            try{
                $new_page_id = $page_id + 1;
                $company_list = $company_service->saveCompanyData($company_list);
                $result = $redis->set($page_id_key, $new_page_id);
                if($result !== true) {
                    throw new \Exception("保存pageid数据到redis出错");
                } else {

                    //记录日志
                    $channel_name_list = $config_service->get('channel_name_list');
                    $channel_name = $channel_name_list['lagou'];
                    $message = "{$channel_name}-招聘公司信息第{$new_page_id}采集成功";
                    $log_service->addInfo($message);
                    
                    /* 此处采用队列只是单纯的业务解耦，将管理的信息采集丢进队列 */

                    //将公司logo采集的信息丢进任务队列中
                    $mq->push(json_encode($company_list), "lagou_company_log");
                    $message = "{$channel_name}-招聘公司信息第{$new_page_id}logo信息采集成功进入队列";
                    $log_service->addInfo($message);
 
                }
            } catch (\Exception $e) {
                $log_service->addInfo($e->getMessage());
            }
		}

	}

	/**
	 * 获取公司姓名
	 */
	private function _getCompanyName($ul_content)
	{
		/* 获取公司名称 */
		$reg_exp_companyname = <<<STR
					#<h3\s*><a.*title\s*=\s*['"](.*)['"].*</h3>#sU
STR;
		preg_match_all($reg_exp_companyname, $ul_content, $matches_companyname);
		$company_name_list = !empty($matches_companyname['1']) ? $matches_companyname['1'] : false;
		return $company_name_list;
	}

	/**
	 * 获取公司链接
	 */
	private function _getCompanyUrl($ul_content)
	{
		/* 获取公司链接 */
		$reg_exp_companyurl= <<<STR
					#<dt\s+class\s*=\s*['"]\s*fl\s*['"]\s*>\s*<a\s+href\s*=\s*['"](.*)['"].*>#sU
STR;
		preg_match_all($reg_exp_companyurl, $ul_content, $matches_companyurl);
		$company_url_list = !empty($matches_companyurl['1']) ? $matches_companyurl['1'] : false;
		return $company_url_list;
	}

	/**
	 * 获取公司介绍
	 */
	private function _getCompanyIntroduce($ul_content)
	{
		/* 获取公司介绍 */
		$reg_exp_company_introduce = <<<STR
					#<p\s*class\s*=\s*['"]\s*details\s*['"]\s+title\s*=\s*['"](.*)?['"]\s*>#sU
STR;
		preg_match_all($reg_exp_company_introduce, $ul_content, $matches_company_introduce);
		$company_introduce_list = !empty($matches_company_introduce['1']) ? $matches_company_introduce['1'] : false;
		return $company_introduce_list;
	}

	/**
	 * 获取公司logo,@todo可能要放到队列中执行
	 */
	private function _getCompanyLogo($ul_content)
	{
		/* 获取公司logo */
		$reg_exp_companylogo = <<<STR
					#data-src\s*=\s*['"](.*)['"]\s*>#sU
STR;
		preg_match_all($reg_exp_companylogo, $ul_content, $matches_company_logo);
		$company_logo_list = !empty($matches_company_logo['1']) ? $matches_company_logo['1'] : false;
		return $company_logo_list;
	}

	/**
	 * 获取公司的领域
	 */
	private function _getCompanyWeb($ul_content)
	{
		/* 获取公司领域 */
		$reg_exp_company_web = <<<STR
			#<span\s+class\s*=\s*['"]fl\s+web['"]\s+title=['"](.*)['"]\s*>#sU
STR;
		preg_match_all($reg_exp_company_web, $ul_content, $matches_company_web);
		$company_web_list = !empty($matches_company_web['1']) ? $matches_company_web['1'] : false;
		return $company_web_list;
	}

	/**
	 * 获取公司的领域
	 */
	private function _getCompanyPlace($ul_content)
	{
		/* 获取公司领域 */
		$reg_exp_company_place = <<<STR
			#<span\s+class\s*=\s*['"]fr\s+place['"]\s+title=['"](.*)['"]\s*>#sU
STR;
		preg_match_all($reg_exp_company_place, $ul_content, $matches_company_place);
		$company_place_list= !empty($matches_company_place['1']) ? $matches_company_place['1'] : false;
		return $company_place_list;
	}

	/**
	 * 获取公司的融资类型
	 */
	private function _getCompanyFinance($ul_content)
	{
		/* 获取公司领域 */
		$reg_exp_company_finance = <<<STR
			#<span\s+class\s*=\s*['"]\s*type\s*['"]\s+title=['"](.*)['"]\s*>#sU
STR;
		preg_match_all($reg_exp_company_finance, $ul_content, $matches_company_finance);
		$company_finance_list = !empty($matches_company_finance['1']) ? $matches_company_finance['1'] : false;
		return $company_finance_list;
    }

}
