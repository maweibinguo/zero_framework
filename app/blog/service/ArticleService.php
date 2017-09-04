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
     * 修改文章
     */
    public function modifyArticle($article_detail)
    {
        //获取原有文章的内容
        $old_article_detail = $this->redis->hGetAll($article_detail['article_id']);

        //首先更新文章的标签
        $article_tag_model = new ArticleTagModel();
        $article_tag_model->editeArticleTag($article_detail, $old_article_detail);

        //更新文章的内容
        $article_model = new ArticleModel();
        $article_model->editeArticle($article_detail, $old_article_detail);
    }

    /**
     * 删除指定的文章
     */
    public function deleteTargetArticle($article_id)
    {
        //获取文章详情
        $article_model = new ArticleModel();
        $article_detail = $article_model->getArticleDetail($article_id);
        if( empty($article_detail) ) {
            $error_message = $this->getErrorMessage('并未查找到该文章', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }

        //基于标签进行删除
        $tag_list = explode(',', $article_detail['tag']);
        $article_tag_model = new ArticleTagModel();
        foreach($tag_list as $tag_name) {
            $article_tag_model->deleteArticleFromTargetTag($article_id, $tag_name);
        }

        //删除该文章
        $article_model->deleteArticleByArticleID($article_id);
    }


    /**
     * 获取文章详情
     */
    public function getArticleDetail($article_id)
    {
        $article_model = new ArticleModel();
        $article_detail = $article_model->getArticleDetail($article_id);
        if(empty($article_detail)) {
            $error_message = $this->getErrorMessage('未能找到该文章，请尝试查看其它文章', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
        return $article_detail;
    }

    /**
     * 给予文章的键获取文章详情
     */
    public function getArticleDetailByKeyName($article_key_name)
    {
        $article_model = new ArticleModel();
        $article_detail = $article_model::$redis->hGetAll($article_key_name);
        return $article_detail;
    }

    /**
     * 获取文章列表
     */
    public function getArticleList($condition = [])
    {
        /* 初始化返回结果 */
        $return_data = [];

        if(!empty($condition['tag'])) {
            $article_tag_model = new ArticleTagModel();
            $return_data = $article_tag_model->getArticleListByTag($condition);
            return $return_data;
        } else {
            $article_list = $this->redis->zRevRange(ArticleModel::ARTICLE_COMMON_LIST, $condition['start'], $condition['end']);
            $article_number = $this->redis->zCard(ArticleModel::ARTICLE_COMMON_LIST);
            $return_data['article_number'] = $article_number;
            $return_data['article_list'] = $article_list;
        }

        return $return_data;
    }

    /**
     * 增加文章的浏览量
     */
    public function incrViewNumber($article_id)
    {
        $view_number = (int)$this->session->get('view_number');
        if($view_number <= 0) {
            $article_model = new ArticleModel();
            $article_model->incrViewNumber($article_id);
        }
    }
}
