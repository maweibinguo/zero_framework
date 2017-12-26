<?php
namespace app\job\console;
use Phalcon\Cli\Task;
use core\base\Zero;
use app\job\models\CityModel as City;

class LagouCityTask extends Task
{
	/**
	 * 采集城市信息
	 */
	public function getCityAction()
	{
		$request_config = [
			'dest'     => 'https://www.lagou.com/gongsi/allCity.html?option=0-0-0'
		];

		/* 匹配最基本的内容,一个li标签就是一个company */
		$request_service = Zero::$app->getDI()->get('curl_request');
		$content = $request_service->getData($request_config);

		/**
		 * 获取排列的字母
		 */
		$reg_letter = <<<STR
			#<div\s+class\s*=\s*['"]word['"]>\s*<span>(\w+)</span>\s*</div>#sU
STR;
		preg_match_all($reg_letter, $content['data'], $letter_matches);
		$letter_list = $letter_matches[1];

		/**
		 * 获取table中的所有ul
		 */
		$reg_ul = <<<STR
			#<ul\s+class\s*=\s*['"]city_list['"]>.*</ul>#sU
STR;
		preg_match_all($reg_ul, $content['data'], $ul_matches);

		/**
		 * 获取城市列表
		 */
		$city_list = [];
		foreach($ul_matches[0] as $ul_content) {
			$reg_city_name = <<<STR
			#<a\s*href\s*=\s*['"]https://www.lagou.com/gongsi/(\d+)-\d+-\d+">(.*)</a>#sU
STR;
			preg_match_all($reg_city_name, $ul_content, $city_matches);
			$city_item = array_combine($city_matches[1], $city_matches[2]);
			$city_list[] = $city_item;
		}

		$city_detail_list = array_combine($letter_list, $city_list);

		/**
		 * 保存城市地址
		 */
		$db_service = Zero::$app->getDI();
		foreach($city_detail_list as $letter => $letter_city_list) {
			foreach($letter_city_list as $city_code => $city_name) {
				$city = new City();
				$city->CityName  = trim($city_name);
				$city->CityCode  = trim($city_code);
				$city->GroupName = trim($letter);
				if($city->save() !== true) {
					echo "\r\n===========================================\r\n";
					echo "保存数据失败\t$city_name\t$city_code\t$letter";
					echo "\r\n===========================================\r\n";
				} else {
					echo "\r\n===========================================\r\n";
					echo "保存数据成功\t$city_name\t$city_code\t$letter";
					echo "\r\n===========================================\r\n";
				}
			}
		}
	}
}
