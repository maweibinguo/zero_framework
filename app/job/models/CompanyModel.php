<?php
namespace App\Company\Models;
use Phalcon\Mvc\Model;

/**
 * 公司
 */
class CompanyModel extends Model
{
    public function initialize()
    {
        $this->setSource("Company");
    }

	/**
	 * 保存公司信息
	 */
	public function saveCompany()
	{
		/* 初始化返回结果 */
		$company_id = 0;

		/* 保存数据 */
		$result = $this->save();
		if($result !== true) {
			return $company_id;
		} else {
			$company_id = $this->CompanyID;
		}

		/* 返回公司编号 */
		return $company_id;
	}

	/**
	 * 基于
	 */
	
}
