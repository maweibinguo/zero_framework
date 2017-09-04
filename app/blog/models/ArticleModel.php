<?php
/**
 * 文章model
 */
namespace app\blog\models;

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

    /* 文章统计集合keyanem */
    const ARTICLE_VIEW_STATISTICS = 'article_view_statistics';

    /* 文章状态 */
    const ARTICLE_STATUS_DRAFT = 0;//草稿
    const ARTICLE_STATUS_PUBLIC = 1;//公布

    /**
     * 保存文章
     */
    public function addArticle($article_detail)
    {
        //保存文章
        $number = static::$redis->incr(static::ARTICLE_COUNT);
        $article_key_name = $this->getKeyName([static::ARTICLE_DETAIL, $number]);
        $article_detail['article_id'] = $article_key_name;
        $result = static::$redis->hMset($article_key_name, $article_detail);
        if($result === false) {
            $error_message = $this->getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
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
            $error_message = $this->getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }

        $is_same = ($article_detail['status'] == $old_article_detail['status']) ? true : false;
        if($is_same === false) {
            if($article_detail['Status'] == static::ARTICLE_STATUS_PUBLIC) {
                //从原有草稿集合中的文章删除
                $delete_number = static::$redis->zDelete(static::ARTICLE_DRAFT_LIST, $article_detail['article_id']);
                if($delete_number <= 0) {
                    $error_message = $this->getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }

                //添加文章到正常集合
                $added_number = static::$redis->zAdd(static::ARTICLE_COMMON_LIST, $article_detail['article_id']);
                if($added_number <= 0) {
                    $error_message = $this->getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }
            } else {
                //从原有正常集合中的文章删除
                $delete_number = static::$redis->zDelete(static::ARTICLE_COMMON_LIST, $article_detail['article_id']);
                if($delete_number <= 0) {
                    $error_message = $this->getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
                    throw new \Exception($error_message);
                }

                //添加文章到草稿集合
                $added_number = static::$redis->zAdd(static::ARTICLE_DRAFT_LIST, $article_detail['article_id']);
                if($added_number <= 0) {
                    $error_message = $this->getErrorMessage('添加文章失败', __CLASS__, __METHOD__, __LINE__);
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
            $error_message = $this->getErrorMessage('删除文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 增加文章的浏览量
     */
    public function incrViewNumber($article_id)
    {
        $scores = static::$redis->zIncrBy(static::ARTICLE_VIEW_STATISTICS, 1, $article_id);
        return $scores;
    }
}
