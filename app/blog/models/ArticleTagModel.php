<?php
/**
 * 文章model
 */
namespace app\blog\models;

class ArticleTagModel extends BaseModel
{
    /* 所有的文章标签keyname */
    const ARTICLE_TAG = 'article:tag:set';

    /* 指定标签下的文章keyname */
    const TAG_OF_ARTICLE = 'tag:%s:article';

    /**
     * 添加文章标签
     */
    public function addArticleTag($article_tag_detail)
    {
        //获取标签
        $article_tag_item = explode(',', $article_tag_detail['tag']);
        $article_tag_item = array_unique($article_tag_item);

        //获取文章的keyname
        $article_key_name = $article_tag_detail['article_key_name'];

        foreach($article_tag_item as $article_tag) {
            //为指定的标签增加一分
            $this->redis->zIncrBy(static::ARTICLE_TAG, 1, $article_tag);

            $key_name = $this->getKeyName([ static::TAG_OF_ARTICLE, $article_tag ]);
            $result = $this->redis->zAdd($key_name, $article_tag_detail['add_time'], $article_key_name);
            if($result == 0) {
                throw new \Exception('添加文章标签失败');       
            }
        }
    }

    /**
     * 基于文章标签获取文章列表
     */
    public function getArticleListByTag()
    {
         
    }
}
