<?php
namespace App\Company\Models;
use Phalcon\Mvc\Model;

class ServiceAreaModel extends Model
{
    public function initialize()
    {
        $this->setSource("ServiceArea");
    }

	/**
	 * 获取服务领域列表
	 */
	public function getServiceAreaList()
	{
		$service_area_list = static::find();
		return $service_area_list->toArray();
	}

	/**
	 * 基于公司服务领域获取编号
	 */
	public function getServiceAreaByName($field = '')
	{
		$service_field_id = 0;
		$field_item = static::findFirst([
													'conditions' => " field = :field: ",
													'bind' => [	'field' => $field],
													'limit' => 1
												]);
		if($field_item) {
			$service_field_id = $field_item->ServiceFieldID;
		}

		return $service_field_id;
	}
}
