<?php
/**
 * 封装了文章的相关业务逻辑
 */
namespace app\blog\service;

use core\base\Components;
use app\blog\models\ArticleModel;
use app\blog\models\ArticleTagModel;

class ArticleService extends Components
{
    /**
     * 添加文章
     */
    public function addAritcle($article_detail)
    {
        //添加文章
        $article_model = new ArticleModel();
        $article_detail['add_time'] = time();
        $article_key_name = $article_model->addArticle($article_detail);

        //添加文章标签
        $article_tag_model = new ArticleTagModel();
        $article_tag_model->addArticleTag([
                                            'tag'              => $article_detail['tag'],
                                            'article_key_name' => $article_key_name,
                                            'add_time'         => $article_detail['add_time']
                                          ]);

        //返回结果
        return $article_key_name;
    }

    /**
     * 获取文章详情
     */
    public function getArticleDetail($key_name)
    {
        $article_model = new ArticleModel();
        $article_detail = $article_model->getArticleDetail($key_name);
        if(empty($article_detail)) {
            throw new \Exception('找不到该文章');   
        }
        return $article_detail;
    }

    /**
     * 获取文章列表
     */
    public function getArticleList($condition)
    {
        /* 初始化返回结果 */
        $article_list = [];

        if(!empty($condition['tag'])) {
                        
        }
    }
}
