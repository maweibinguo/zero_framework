<?php
/**
 * 格言model
 */
namespace app\blog\models;
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
}
