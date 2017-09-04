<?php
namespace core\base;

use \Firebase\JWT\JWT as JwtComponent;
use core\base\Components;

class Jwt extends Components
{
    /**
     * jwt秘钥
     */
    public $secure_key = '';

    /**
     * 加密方式
     */
    public $encrypt_type = [    'HS256'    ];

    /**
     * 初始化相关数据
     */   
    public function __construct()
    {
        $jwt_config_list = $this->config->get('jwt');
        $this->secure_key = $jwt_config_list['secure_key'];
    }

    /**
     * 对数据进行加密
     */
    public function encrypt($data)
    {
        return JwtComponent::encode($data, $this->secure_key);
    }

    /**
     * 对数据进行解密
     */
    public function decrypt($data)
    {
        return JwtComponent::decode($data, $this->secure_key, $this->encrypt_type);
    }
}
