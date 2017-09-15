<?php
/**
 * 用户model
 */
namespace app\admin\models;

use core\base\Log;

class UserModel extends BaseModel
{
    /* 用户有序集合 */
    const USER_ZSORT_LIST = 'admin:user:zsort:list';

    /* 用户详细信息 */
    const USER_HASH_DETAIL = 'admin:user:%s:detail';

    /* 用户自增编号 */
    const USER_STR_NUMBER = 'admin:user:number';

    /**
     * 添加用户
     */
    public function addUser($user_data)
    {
        /* 获取用户编号 */ 
        $user_id = static::$redis->incr(static::USER_STR_NUMBER);

        $scores = static::$redis->zScore(static::USER_ZSORT_LIST, $user_data['user_name']);
        if($scores > 0) {
            $error_message = Log::getErrorMessage('该用户名已经存在, 请更换其他用户名注册');
            throw new \Exception($error_message);
        }

        /* 添加到有序集合 */
        $add_number = static::$redis->zAdd(static::USER_ZSORT_LIST, $user_id, $user_data['user_name']);
        if($add_number == 0) {
            $error_message = Log::getErrorMessage('注册用户失败');
            throw new \Exception($error_message);
        }

        /* 添加用户详情信息 */
        $user_key_name = $this->getKeyName([    static::USER_HASH_DETAIL,
                                                $user_id    ]);
        $user_data['password'] = password_hash($user_data['password'], PASSWORD_DEFAULT);
        $set_result = static::$redis->hMSet($user_key_name, $user_data);
        if($set_result === false) {
            $error_message = Log::getErrorMessage('保存用户注册信息失败');
            throw new \Exception($error_message);
        }
    }

    /**
     * 获取用户详细信息
     */
    public function getUserDetail($user_name)
    {
        $user_id = static::$redis->zScore(static::USER_ZSORT_LIST, $user_name); 
        if($user_id <= 0) {
            $error_message = Log::getErrorMessage('获取用户编号失败');
            throw new \Exception($error_message);
        }
        $user_key_name = $this->getKeyName([    static::USER_HASH_DETAIL,
                                                $user_id    ]);
        $user_detail = static::$redis->hGetAll($user_key_name);
        if(!is_array($user_detail) || empty($user_detail)) {
            $error_message = Log::getErrorMessage('用户名或者密码错误');
            throw new \Exception($error_message);
        }
        return $user_detail;
    }

    /**
     * 执行登录动作
     */
    public function login($user_name, $password)
    {
        $login_result = false;
        $user_detail = $this->getUserDetail($user_name);
        $validate_result = password_verify($password, $user_detail['password']);
        if($validate_result === false) {
            $error_message = Log::getErrorMessage('用户名或者密码错误');
            throw new \Exception($error_message);
        }
        return $user_detail;
    }
}
