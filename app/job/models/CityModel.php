<?php
namespace app\job\models;
use Phalcon\Mvc\Model;

/**
 * 城市
 */
class CityModel extends Model
{
    public function initialize()
    {
        $this->setSource("City");
    }

	/**
	 * 基于城市名称获取城市编号
	 */
	public function getCityIDByName($city_name = '')
	{
		$city_id = 0;

		$city_item = static::findFirst([
			'conditions' => ' CityName = :CityName: ',
			'bind' => [
				'CityName' => $city_name
			],
			'limit' => 1,
		]);	

		if($city_item) {
			$city_id = $city_item->CityID;
		}

		return $city_id;
	}

	/**
	 * 获取城市详情
	 */
	public function getCityDetailByCodeName($city_code = '')
	{
		$city_detail = [];

		$result = static::findFirst([
								'conditions' => ' CityCode = :CityCode: ',
								'bind'       => [
													'CityCode' => $city_code
												],
								'limit'		 => 1
							]);
		if($result) {
			$city_detail = $result->toArray();
		}
		return $city_detail;
	}

}
