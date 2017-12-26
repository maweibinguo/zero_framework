<?php
/**
 * 封装了轮播图的相关逻辑
 */
namespace app\admin\service;

use app\admin\models\PictureModel;
use core\base\Components;

class PictureService extends Components
{
    /**
     * 页面样式
     */
    public $page_css = [
                           '/css/checkbox3.min.css'
                       ];
    /**
     * 添加轮播图
     */
    public function addPicture($picture_item)
    {
        $picture = new PictureModel(); 
        $picture->addPicture($picture_item);
    }

    /**
     * 获取轮播图列表
     */
    public function getPictureList($condition)
    {
        $picture_list = [];

        $picture = new PictureModel(); 
        $picture_list = $picture->getPictureList($condition);

        return $picture_list;
    }

    /**
     * 获取轮播图详情
     */
    public function getPictureDetail($picture_id)
    {
        $picture = new PictureModel(); 
        $picture_detail = $picture->getPictureDetail($picture_id);
        return $picture_detail;
    }

    /**
     * 修改轮播图
     */
    public function modifyPicture($picture_item)
    {
        $picture = new PictureModel(); 
        $picture->modifyPicture($picture_item);
    }
}
