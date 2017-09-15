<?php
/**
 * 文章类别model
 */
namespace app\blog\models;

use app\blog\models\ArticleModel;
use core\base\Log;

class ArticleCategoryModel extends BaseModel
{
    /* 文章类别 */
    const ARTICLE_CATEGORY = 'article_category_%s';

    /**
     * 修改文章类别
     */
    public function editeCategoryForArticle($article_detail, $old_article_detail )
    {
        $is_same = ($article_detail['status'] == $old_article_detail['status']) ? true : false;

        switch($article_detail['status']) {
            case ArticleModel::ARTICLE_STATUS_DRAFT:
                if($is_same === true) {
                    //do nothing 
                } else {
                    $this->deleteArticleFromCategory($article_detail['article_id'], $old_article_detail['category']);
                }
                break;
            case ArticleModel::ARTICLE_STATUS_PUBLIC:
                if($is_same === true) {
                    if( $article_detail['category'] != $old_article_detail['category'] ) {
                        $this->addArticleToCategory($article_detail['article_id'], $article_detail['category'], $article_detail['add_time']);
                        $this->deleteArticleFromCategory($article_detail['article_id'], $old_article_detail['category']);
                    }
                } else {
                    $this->addArticleToCategory($article_detail['article_id'], $article_detail['category'], $article_detail['add_time']);
                }
                break;
        }
    }

    /**
     * 将文章添加到文章类别列表中
     */
    public function addArticleToCategory($article_id, $category, $add_time)
    {
        $key_name = $this->getKeyName([ static::ARTICLE_CATEGORY, $category ]);
        $add_number = static::$redis->zAdd($key_name, $add_time, $article_id);
        if($add_number == 0) {
            $error_message = Log::getErrorMessage('将文章添加到对应类别中失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 删除对应类别下的文章
     */
    public function deleteArticleFromCategory($article_id, $category)
    {
        $key_name = $this->getKeyName([ static::ARTICLE_CATEGORY, $category ]);
        $result = static::$redis->zDelete($key_name, $article_id);
        if($result == 0) {
            $error_message = Log::getErrorMessage('从指定类别中删除文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
    }

    /**
     * 基于category获取文章列表
     */
    public function getArticleListByCategory($condition)
    {
        $return_data = [];

        $key_name = $this->getKeyName([ static::ARTICLE_CATEGORY, $condition['category'] ]);
        $article_number = static::$redis->zCard($key_name);
        $return_data['article_number'] = $article_number;

        $article_list = static::$redis->zRevRange(  $key_name, 
                                                    $condition['start'], 
                                                    $condition['page_size']
                                                      );
        $return_data['article_list'] = $article_list;

        return $return_data;
    }
}
