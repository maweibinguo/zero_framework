<?php
/**
 * 格言model
 */
namespace app\admin\models;
use core\base\Log;

class MottoModel extends BaseModel
{
    const MOTTO_SET = 'motto_set';

    /**
     * 获取格言
     */
    public function getMotto()
    {
        $motto = static::$redis->sRandMember(static::MOTTO_SET);    
        return $motto;
    }

    /**
     * 添加格言
     */
    public function addMotto($content)
    {
        $result = static::$redis->sAdd(static::MOTTO_SET, $content);
        if(empty($result)) {
            $error_message = Log::getErrorMessage('添加格言失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 获取格言的数量
     */
    public function getNumber()
    {
        $number = static::$redis->sCard(static::MOTTO_SET);
        return $number;
    }
}
