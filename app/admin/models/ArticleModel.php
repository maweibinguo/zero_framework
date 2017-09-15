<?php
/**
 * 文章model
 */
namespace app\admin\models;
use core\base\Log;

class ArticleModel extends BaseModel
{
    /* 文章计数keyname */
    const ARTICLE_COUNT = 'article:count';

    /* 文章内容keyname */
    const ARTICLE_DETAIL = 'article:detail:%d';

    /* 正常文章列表的keyname */
    const ARTICLE_COMMON_LIST = 'article:common:list';

    /* 草稿文章列表的keyname */
    const ARTICLE_DRAFT_LIST = 'article:draft:list';

    /* 热门文章的keyname */
    const ARTICLE_HOT = 'article:hot';

    /* 文章状态 */
    const ARTICLE_STATUS_DRAFT = 0;//草稿
    const ARTICLE_STATUS_PUBLIC = 1;//公布

    /* 是否热门文章 */
    const HOT_STATUS_COMMON = 1; //热门
    const HOT_STATUS_DISABLE = 0; //非热门

    /**
     * 保存文章
     */
    public function addArticle($article_detail)
    {
        //保存文章
        if(!isset($article_detail['article_view_statistics']) || 
                                                                (int)$article_detail['article_view_statistics'] <= 0 ) {
            $article_detail['article_view_statistics'] = 1;    
        }
        $number = static::$redis->incr(static::ARTICLE_COUNT);
        $article_key_name = $this->getKeyName([static::ARTICLE_DETAIL, $number]);
        $article_detail['article_id'] = $article_key_name;
        $result = static::$redis->hMset($article_key_name, $article_detail);
        if($result === false) {
            $error_message = Log::getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }

        //向有序集合中添加文章keyname 分数为添加时间
        switch($article_detail['status']) {
           case static::ARTICLE_STATUS_DRAFT:
                $length = static::$redis->zAdd(static::ARTICLE_DRAFT_LIST, $article_detail['add_time'], $article_key_name);
                break;
           case static::ARTICLE_STATUS_PUBLIC:
                static::$redis->zAdd(static::ARTICLE_COMMON_LIST, $article_detail['add_time'], $article_key_name);
                break;
        }

        return $article_key_name;
    }

    /**
     * 修改文章内容
     */
    public function editeArticle($article_detail, $old_article_detail)
    {
        $result = static::$redis->hMset($article_detail['article_id'], $article_detail);
        if($result === false) {
            $error_message = Log::getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }

        $is_same = ($article_detail['status'] == $old_article_detail['status']) ? true : false;
        if($is_same === false) {
            if($article_detail['status'] == static::ARTICLE_STATUS_PUBLIC) {
                //从原有草稿集合中的文章删除
                $delete_number = static::$redis->zDelete(static::ARTICLE_DRAFT_LIST, $article_detail['article_id']);
                if($delete_number <= 0) {
                    $error_message = Log::getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }

                //添加文章到正常集合
                $added_number = static::$redis->zAdd(static::ARTICLE_COMMON_LIST, $article_detail['add_time'], $article_detail['article_id']);
                if($added_number <= 0) {
                    $error_message = Log::getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }
            } else {
                //从原有正常集合中的文章删除
                $delete_number = static::$redis->zDelete(static::ARTICLE_COMMON_LIST, $article_detail['article_id']);
                if($delete_number <= 0) {
                    $error_message = Log::getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }

                //添加文章到草稿集合
                $added_number = static::$redis->zAdd(static::ARTICLE_DRAFT_LIST, $article_detail['add_time'], $article_detail['article_id']);
                if($added_number <= 0) {
                    $error_message = Log::getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }
            }
        }
    }

    /**
     * 获取文章详情
     */
    public function getArticleDetail($article_id)
    {
        return static::$redis->hGetAll($article_id);
    }

    /**
     * 删除文章
     */
    public function deleteArticleByArticleID($article_id)
    {
        $deleted_number = static::$redis->delete($article_id);
        if($deleted_number <= 0) {
            $error_message = Log::getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 从列表中删除文章
     */
    public function deleteArticleFromList($article_id, $article_status)
    {
        switch($article_status) {
            case static::ARTICLE_STATUS_DRAFT:
                static::$redis->zDelete(static::ARTICLE_DRAFT_LIST, $article_id);
                break;
            case static::ARTICLE_STATUS_PUBLIC:
                static::$redis->zDelete(static::ARTICLE_COMMON_LIST, $article_id);
                break;
            default:
                break;
        }
    }

    /**
     * 增加文章的浏览量
     */
    public function incrViewNumber($article_id)
    {
        $view_number = static::$redis->hIncrBy($article_id, 'article_view_statistics', 1);
        return $view_number;
    }

    /**
     * 获取草稿列表
     */
    public function getDraftArticleList($condition)
    {
        $return_data = [];

        $article_number = static::$redis->zCard(static::ARTICLE_DRAFT_LIST);
        $return_data['article_number'] = $article_number;

        $article_list = static::$redis->zRevRange(  static::ARTICLE_DRAFT_LIST, 
                                                    $condition['start'], 
                                                    $condition['end']
                                                      );
        $return_data['article_list'] = $article_list;

        return $return_data;
    }

    /**
     * 添加热门文章
     */
    public function addHotArticle($article_id)
    {
        $result = static::$redis->zadd(static::ARTICLE_HOT, time(), $article_id);
        if($result <= 0) {
            $error_message = Log::getErrorMessage('添加热门文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 删除热门文章
     */
    public function deleteHotArticle($article_id)
    {
        $result = static::$redis->zDelete(static::ARTICLE_HOT, $article_id);
        if($result == 0) {
            $error_message = Log::getErrorMessage('从热门文章key删除删除'.$article_id.'失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 获取热门文章
     */
    public function getHotArticle()
    {
        $article_id_list = static::$redis->zRevRange(static::ARTICLE_HOT, 0, 0);
        $article_id = $article_id_list[0];
        $article_detail = static::$redis->hGetAll($article_id);
        if(empty($article_detail)) {
            $error_message = Log::getErrorMessage('获取热门文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        } else {
            return $article_detail;
        }
    }

    /**
     * 修改热门文章
     */
    public function editeHotArticle($article_detail, $old_article_detail)
    {
        $is_same = ($article_detail['status'] == $old_article_detail['status']) ? true : false;
        $article_id = $article_detail['article_id'];

        switch($article_detail['status']) {
            case ArticleModel::ARTICLE_STATUS_DRAFT://现在是草稿
                if($is_same === true) {//两次都是草稿,草稿没有热门文章对应的key
                    //do nothing 
                } else {//由正常转换为草稿,但是只有原来是正常的热门文章才会有对应的key
                    if($old_article_detail['ishot'] != static::HOT_STATUS_COMMON) return false;
                    $this->deleteHotArticle($article_id);
                }
                break;
            case ArticleModel::ARTICLE_STATUS_PUBLIC://现在是正常
                if($is_same === true) {//两次都是正常文章
                    $is_hot_same = ($article_detail['ishot'] == $old_article_detail['ishot']) ? true : false;
                    switch($article_detail['ishot']) {
                        case ArticleModel::HOT_STATUS_COMMON://现在是hot原来不是
                            if($is_hot_same === false) {
                                $this->addHotArticle($article_id);
                            }
                            break;
                        case ArticleModel::HOT_STATUS_DISABLE://现在不是hot原来是
                            if($is_hot_same === false) {
                                $this->deleteHotArticle($article_id);
                            }
                            break;
                    }
                } else {//原来是草稿，现在是正常,但是只有设置为热门才会添加到热门key
                    if($article_detail['ishot'] != static::HOT_STATUS_COMMON) return false;
                    $this->addHotArticle($article_id);
                }
                break;
        }
    
    }

    /**
     * 获取草稿文章的数量
     */
    public function getDraftArticleNumber()
    {
        $article_number = static::$redis->zCard(static::ARTICLE_DRAFT_LIST);
        return  $article_number;
    }

    /**
     * 获取正常文章的数量
     */
    public function getCommonArticleNumber()
    {
        $article_number = static::$redis->zCard(static::ARTICLE_COMMON_LIST);
        return  $article_number;
    }

}
