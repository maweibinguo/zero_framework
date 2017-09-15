<?php
/**
 * 轮播图model
 */
namespace app\admin\models;
use core\base\Log;

class PictureModel extends BaseModel
{
    /* 轮播图计数器 */
    const PICTURE_COUNT = 'picture_count';

    /* 存放轮播图列表的key */
    const PICTURE_ALL = 'picture_all';
    const PICTURE_COMMON = 'picture_common';
    const PICTURE_DISABLED = 'picture_disabled';

    /* 轮播图状态 */
    const PICTURE_STATUS_COMMON = 1;//正常
    const PICTURE_STATUS_DISABLED = 0;//禁用

    /* 轮播图详情 */
    const PICTURE_DETAIL = 'picture:detail:%s';

    /**
     * 添加轮播图
     */
    public function addPicture($picutre_item)
    {
        //计数器
        $picture_id = static::$redis->incr(static::PICTURE_COUNT);
        $key_name = $this->getKeyName([static::PICTURE_DETAIL, $picture_id]);

        //保存详情
        $picutre_item['picture_id'] = $picture_id;
        $result = static::$redis->hMset($key_name, $picutre_item);
        if($result === false) {
            $error_message = Log::getErrorMessage('添加轮播图详情失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
        
        //保存key到集合中
        $add_number = static::$redis->zAdd(static::PICTURE_ALL, $picutre_item['add_time'], $key_name);
        if($add_number <= 0) {
            $error_message = Log::getErrorMessage('添加轮播图keyname到所有轮播图集合中失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
        
        //保存key到对应集合中
        switch($picutre_item['status']) {
            case static::PICTURE_STATUS_COMMON://正常
                $add_number = static::$redis->zAdd(static::PICTURE_COMMON, $picutre_item['add_time'], $key_name);
                break;
            case static::PICTURE_STATUS_DISABLED://停用
                $add_number = static::$redis->zAdd(static::PICTURE_DISABLED, $picutre_item['add_time'], $key_name);
                break;
            default:
                $error_message = Log::getErrorMessage('轮播图状态不正确', __CLASS__, __METHOD__, __LINE__);
                throw new \Exception($error_message);
                break;
        }

        if($add_number <= 0) {
            $error_message = Log::getErrorMessage('添加轮播图keyname到轮播图集合中失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 修改轮播图
     */
    public function modifyPicture($picutre_item)
    {
        $old_picture_item = $this->getPictureDetail($picutre_item['picture_id']);
        $is_same = ($picutre_item['status'] == $old_picture_item['status']) ? true : false;
        switch($picutre_item['status']) {
            case static::PICTURE_STATUS_COMMON:
                if($is_same === false) {
                    $result = static::$redis->multi()
                                            ->sRem(static::PICTURE_DISABLED, $picutre_item['picture_id'])
                                            ->sAdd(static::PICTURE_COMMON, $picutre_item['picture_id'])
                                            ->exec();
                    if(empty($result)) {
                        $error_message = Log::getErrorMessage('修改轮播图失败', __CLASS__, __METHOD__, __LINE__);
                        throw new \Exception($error_message);
                    }
                }
                break;
            case static::PICTURE_STATUS_DISABLED:
                if($is_same === false) {
                    $result = static::$redis->multi()
                                            ->sRem(static::PICTURE_COMMON, $picutre_item['picture_id'])
                                            ->sAdd(static::PICTURE_DISABLED, $picutre_item['picture_id'])
                                            ->exec();
                    if(empty($result)) {
                        $error_message = Log::getErrorMessage('修改轮播图失败', __CLASS__, __METHOD__, __LINE__);
                        throw new \Exception($error_message);
                    }
                }
                break;
        }

        //保存详情
        $result = static::$redis->hMset($picutre_item['picture_id'], $picutre_item);
        if($result === false) {
            $error_message = Log::getErrorMessage('添加轮播图详情失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 获取轮播图
     */
    public function getPictureList($condition)
    {
        $condition['status'] = isset($condition['status']) ? $condition['status'] : -1;
        switch($condition['status']) {
            case static::PICTURE_STATUS_COMMON:
                $pircture_list = static::$redis->zRevRange(static::PICTURE_COMMON, 0, -1);
                break;
            case static::PICTURE_STATUS_DISABLED:
                $pircture_list = static::$redis->zRevRange(static::PICTURE_DISABLED, 0, -1);
                break;
            default:
                $pircture_list = static::$redis->zRevRange(static::PICTURE_ALL, 0, -1);
                break;
        }

        return $pircture_list;
    }

    /**
     * 获取轮播图详情
     */
    public function getPictureDetail($picture_id)
    {
        $picutre_detail = static::$redis->hGetAll($picture_id);
        if(is_array($picutre_detail)) {
            $picutre_detail['picture_id'] = $picture_id;
        } else {
            $picutre_detail = false;       
        }
        return $picutre_detail;
    }
}
