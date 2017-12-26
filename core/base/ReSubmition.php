<?php
/**
 * 自定义框架请求类
 */
namespace core\base;

class ReSubmition extends Components
{
    /**
     * 唯一值的name
     */
    private $name = 'resubmit';

    /**
     * 获取唯一串
     */
    public function getUniqueValue()
    {
        $unique_value = microtime() . mt_rand(00001, 99999) . uniqid();
        return hash('sha256', $unique_value);
    }

    /**
     * 校验是否是重复提交
     */
    public function isReSumit($unique_value)
    {
        $result = $this->redis->setNx($unique_value, 1);   
        if($result === true) {
            $this->redis->expire($unique_value, 5);
        }
        return $result;
    }

    /**
     * 设置名称
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 获取名称
     */
    public function getName()
    {
        return $this->name;
    }
}
