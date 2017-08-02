<?php
namespace App\Company\Models;
use Phalcon\Mvc\Model;
use Phalcon\Db\Profiler as DbProfiler;

/**
 * 公司扩展信息
 */
class CompanyExtendModel extends Model
{
    public function initialize()
    {
        $this->setSource("CompanyExtend");
    }

	/**
	 * 基于公司编号获取公司详情
	 */
	public function getDetailByCompanyID($company_id)
	{
		/* 初始化返回结果 */
		$detail_item = [];	

		/* 获取记录 */
		$object_item = static::findFirst([
										'conditions' => 'CompanyID = :CompanyID:',
										'bind'		 => [
															'CompanyID' => $company_id
														]
									]);
		$detail_item = $object_item->toArray();

		/* 返回最终结果 */
		return $detail_item;
	}

	/**
	 * 基于公司编号获取公司详情
	 */
	public function getDetailByExtendCompanyID($company_extend_id)
	{
		/* 初始化返回结果 */
        $object_item;

		/* 获取记录 */
		$object_item = static::findFirst([
										'conditions' => 'CompanyExtendID= :CompanyExtendID:',
										'bind'		 => [
															'CompanyExtendID' => $company_extend_id
														],
									]);
		/* 返回最终结果 */
		return $object_item;
	}

	/**
	 * 保存公司的扩展信息
	 */
	public function saveExtendData()
	{
		/* 初始化返回结果 */
		$company_extend_id = 0;

		/* 保存扩展信息 */
		$result = $this->save();
		if($result === true) {
			$company_extend_id = $this->CompanyExtendID;	
		}

		/* 返回编号信息 */
		return $company_extend_id;
	}

	/**
	 * 基于渠道编号和外部公司的编号获取内部系同的公司编号
	 */
	public function getCompanyID($channel_id, $outer_company_id)
	{
		$company_id = 0;

		$result_item = static::findFist([
			'conditions' => ' ChanelID = :ChanelID: and OuterCompanyID = :OuterCompanyID: ',
			'columns'	 => ' CompanyID ',
			'binds'		 => [
							'ChanelID' => $channel_id,
							'OuterCompanyID' => $outer_company_id
							],
			'limit'		 => 1
		]);
		if($result_item) {
			$company_id = $result_item->CompanyID;
		}

		return $company_id;
	}
}
