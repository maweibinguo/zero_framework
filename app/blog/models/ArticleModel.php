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

    /* 文章状态 */
    const ARTICLE_STATUS_DRAFT = 0;//草稿
    const ARTICLE_STATUS_PUBLIC = 1;//公布

    /**
     * 保存文章
     */
    public function addArticle($article_detail)
    {
        //保存文章
        $number = $this->redis->incr(static::ARTICLE_COUNT);
        $article_key_name = $this->getKeyName([static::ARTICLE_DETAIL, $number]);
        $result = $this->redis->hMset($article_key_name, $article_detail);
        if($result === false) {
            throw new \Exception('添加文章失败');
        }

        //向有序集合中添加文章keyname 分数为添加时间
        switch($article_detail['status']) {
           case static::ARTICLE_STATUS_DRAFT:
                $length = $this->redis->zAdd(static::ARTICLE_DRAFT_LIST, $article_detail['add_time'], $article_key_name);
                break;
           case static::ARTICLE_STATUS_PUBLIC:
                $this->redis->zAdd(static::ARTICLE_COMMON_LIST, $article_detail['add_time'], $article_key_name);
                break;
        }

        return $article_key_name;
    }

    /**
     * 获取文章详情
     */
    public function getArticleDetail($article_key_name)
    {
        return $this->redis->hGetAll($article_key_name);
    }
}
