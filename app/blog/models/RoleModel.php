<?php
/**
 * 用户model
 */
namespace app\blog\models;

use core\base\Log;

class RoleModel extends BaseModel
{
    /**
     * 角色
     */
    const ROLE_TYPE_ADMIN = 1; //管理员

    const ROLE_TYPE_NORL = 2; //普通用户

    /**
     * 角色名称
     */
    public static $role_name_list = [
        self::ROLE_TYPE_ADMIN => '管理员',
        self::ROLE_TYPE_NORL => '用户'
    ];

    /**
     * 获取名称
     */
    public function getRoleName($type_id)
    {
       return $role_name_list[$type_id]; 
    }
}
