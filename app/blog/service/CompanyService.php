<?php
namespace Console\Service;
use Core\Zero; 
use app\job\models\CompanyModel;
use app\job\models\CityModel;
use app\job\models\CompanyExtendModel;
use app\job\models\FinanceScaleModel;

/**
 * 封装了公司相关的服务
 */
class CompanyService
{

    /**
     * 公司服务的日志频道
     */
    const COMPANY_SERVICE_LOG  = 'compnay_service_log';

	/**
	 * 保存采集的company信息
	 */
	public function saveCompanyData($company_list = [])
	{
		if(empty($company_list)) {
			throw new \Exception("要保存的企业信息为空");
        }

		//添加城市编码
		$company_list = $this->_addCityIDToList($company_list);	

		//添加资金规模
        $company_list = $this->_addFinanceScaleToList($company_list);

        //添加外部公司编号
        $company_list = $this->_addOuterCompanyIDToList($company_list);

		//开启事物
        $db_service = Zero::$app->service->get('db');
        $db_service->begin();

        foreach($company_list as $company_key => $company_item) {

            //保存company主表信息
			$company = new CompanyModel();
			$company->CompanyName		= $company_item['company_name'];
			$company->CompanyIntroduce	= $company_item['company_introduce'];
			$company->CityID			= $company_item['city_id'];
			$company->FinaceScaleID		= $company_item['finance_scale_id'];
			$company->ChannleID			= $company_item['channel_id'];
			$company->AddTime			= date("Y-m-d H:i:s");
			if( ($company_id = $company->saveCompany()) <= 0 ) {
				throw new \Exception("添加公司信息失败");
                $db_service->rollback();
            }

            //保存company_extend从表信息
            $company_extend = new CompanyExtendModel();
            $company_extend->CompanyID = $company_id;
            $company_extend->ChannelID = $company_item['channel_id'];
            $company_extend->OuterCompanyID = $company_item['outer_company_id'];
            $company_extend->PropertyValue= json_encode([  'CompanyUrl' => $company_item['company_url'],
                'CompanyLogo' => $company_item['company_logo']  ]);
            if( ($company_extend_id = $company_extend->saveExtendData()) <= 0 ) {
				throw new \Exception("添加公司扩展信息失败");
                $db_service->rollback();
            }

            //公司扩展信息编号
            $company_item['company_extend_id'] = $company_extend_id;

            //公司主体信息编号
            $company_item['company_id'] = $company_id;

            $company_list[$company_key] = $company_item;
		}

        $db_service->commit();

        /* 返回最终结果 */
        return $company_list;
	}

	/**
	 * 获取公司的所在城市编码
	 */
	private function _addCityIDToList($company_list)
	{
		$city = new CityModel();

		foreach($company_list as $company_key => $company_item)	{
			if( empty($company_item['company_place']) ) {
				throw new \Exception("公司地址为空");	
			}
			$city_code = $city->getCityIDByName( $company_item['company_place'] );
			$company_item['city_id'] = $city_code;
			$company_list[$company_key] = $company_item;
		}

		return $company_list;
	}

	/**
	 * 获取公司的融资规模
	 */
	private function _addFinanceScaleToList($company_list)
	{
		$finance_scale = new FinanceScaleModel();

		foreach($company_list as $company_key => $company_item)	{
			if( empty($company_item['company_finance']) ) {
				throw new \Exception("公司融资规模为空");	
			}
			$finace_scale_id = $finance_scale->getFinanceIDByName( $company_item['company_finance'] );
			if($finace_scale_id <= 0) {
				throw new \Exception("没有找到对应的融资规模编号");	
			}

			$company_item['finance_scale_id'] = $finace_scale_id;
			$company_list[$company_key] = $company_item;
		}

		return $company_list;
	}

	/**
	 * 获取公司的行业领域
	 */
	private function _addServiceAreaToList()
	{
    }

    /**
     * 添加外部订单号到列表中
     */
    private function _addOuterCompanyIDToList($company_list)
    {

		foreach($company_list as $company_key => $company_item)	{
			if( empty($company_item['company_url']) ) {
				throw new \Exception("公司链接地址为空");	
			}

            $outer_company_id = explode("/", $company_item['company_url']);
            $company_item['outer_company_id'] = (int)end($outer_company_id);
			$company_list[$company_key] = $company_item;
		}

		return $company_list;
    }
}
