<?php
namespace App\Company\Models;
use Phalcon\Mvc\Model;

/**
 * 公司规模
 */
class FinanceScaleModel extends Model
{
	/**
	 * 设置表名称
	 */
    public function initialize()
    {
        $this->setSource("FinanceScale");
    }

	/**
	 * 获取公司规模列表
	 */
	public function getFinanceScaleList()
	{
		$finance_scale_list = static::find();
		return $finance_scale_list->toArray();
	}

	/**
	 * 基于公司规模获取规模编号,@todo此处可能要加索引
	 */
	public function getFinanceIDByName($finance_name)
	{
		$finaceScaleID = 0;
		$finance_scale_item = static::findFirst([
													'conditions' => " Scale = :Scale: ",
													'bind' => [	'Scale' => $finance_name	],
													'limit' => 1
												]);
		if($finance_scale_item) {
			$finaceScaleID = $finance_scale_item->FinaceScaleID;
		}

		return $finaceScaleID;
	}
}
