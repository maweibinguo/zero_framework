<?php
/**
 * 基类Model
 */
namespace app\blog\models;

use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    /**
     * redis服务
     */
    public static $redis;

    /**
     * 初始化相关属性
     */
    public function initialize()
    {
        static::$redis = $this->di->get('redis'); 
    }

    /**
     * 获取指定的keyname
     */
    protected function getKeyName($params)
    {
        $key_name = call_user_func_array('sprintf', $params);
        if($key_name === false) {
            throw new \Exception('获取keyname失败');
        }
        return $key_name;
    }
}
