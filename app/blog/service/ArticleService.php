<?php
/**
 * 封装了文章的相关业务逻辑
 */
namespace app\blog\service;

use core\base\Components;
use app\blog\models\ArticleModel;
use app\blog\models\ArticleTagModel;
use app\blog\models\ArticleCategoryModel;
use app\blog\models\MottoModel;
use core\base\Log;

class ArticleService extends Components
{
    /**
     * 添加文章
     */
    public function addAritcle($article_detail)
    {
        //添加文章
        $article_model = new ArticleModel();
        $article_tag_model = new ArticleTagModel();
        $article_category = new ArticleCategoryModel();
        $article_detail['add_time'] = time();
        $article_key_name = $article_model->addArticle($article_detail);

        if($article_detail['status'] == ArticleModel::ARTICLE_STATUS_PUBLIC) {
            //添加文章标签
            $article_tag_model->addArticleTag([
                                                'tag'              => $article_detail['tag'],
                                                'article_key_name' => $article_key_name,
                                                'add_time'         => $article_detail['add_time']
                                            ]);

            //添加文章类别
            $article_category->addArticleToCategory($article_key_name, $article_detail['category'], $article_detail['add_time']);

            //添加今日推荐
            if($article_detail['ishot'] == ArticleModel::HOT_STATUS_COMMON) {
                $article_model->addHotArticle($article_key_name);
            }
        }

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

        //更新文章所在的类别
        $article_category = new ArticleCategoryModel();
        $article_category->editeCategoryForArticle($article_detail, $old_article_detail);

        //更新文章的内容
        $article_model = new ArticleModel();
        $article_model->editeHotArticle($article_detail, $old_article_detail);
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
            $error_message = Log::getErrorMessage('并未查找到该文章', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }

        if($article_detail['status'] == ArticleModel::ARTICLE_STATUS_PUBLIC) {

            //基于标签进行删除
            $tag_list = explode(',', $article_detail['tag']);
            $article_tag_model = new ArticleTagModel();
            foreach($tag_list as $tag_name) {
                $article_tag_model->deleteArticleFromTargetTag($article_id, $tag_name);
            }

            //在对应的类别下删除 
            $article_category = new ArticleCategoryModel();
            $article_category->deleteArticleFromCategory($article_id, $article_detail['category']);
        }

        //删除该文章
        $article_model->deleteArticleByArticleID($article_id);

        //从文章列表中进行删除
        $article_model->deleteArticleFromList($article_id, $article_detail['status']);
    }


    /**
     * 获取文章详情
     */
    public function getArticleDetail($article_id)
    {
        $article_model = new ArticleModel();
        $article_detail = $article_model->getArticleDetail($article_id);
        if(empty($article_detail)) {
            $error_message = Log::getErrorMessage('未能找到该文章，请尝试查看其它文章', __CLASS__, __METHOD__, __LINE__);
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
        } elseif(!empty($condition['category'])) {
            $article_category_model = new ArticleCategoryModel();
            $return_data = $article_category_model->getArticleListByCategory($condition);
            return $return_data;
        } elseif(isset($condition['status']) && $condition['status'] == ArticleModel::ARTICLE_STATUS_DRAFT) {
            $article_model = new ArticleModel();
            $return_data = $article_model->getDraftArticleList($condition);
            return $return_data;
        } elseif(isset($condition['status']) && $condition['status'] == ArticleModel::ARTICLE_STATUS_PUBLIC) {
            $article_model = new ArticleModel();
            $return_data = $article_model->getCommonArticleList($condition);
            return $return_data;
        } else {
            $article_model = new ArticleModel();
            $return_data = $article_model->getAllArticleList($condition);
            return $return_data;
        }
    }

    /**
     * 增加文章的浏览量
     */
    public function incrViewNumber($article_id)
    {
        $article_view_statistics = (int)$this->session->get('article_view_statistics');
        if($article_view_statistics <= 0) {
            $article_model = new ArticleModel();
            $article_view_statistics = $article_model->incrViewNumber($article_id);
            $this->session->set('article_view_statistics', $article_view_statistics);
        }
    }

    /**
     * 获取标签
     */
    public function getTagList()
    {
        $tag_list_format = [];

        $article_tag_model = new ArticleTagModel(); 
        $tag_list = $article_tag_model->getTagList();
        if(is_array($tag_list)) {
            $row_number = 0;
            $count = 0;
            foreach($tag_list as $tag_name => $tag_score) {
                if( ($count % 3) == 0) {
                    $row_number++;
                }
                $tag_list_format[$row_number][] = [ 'tag_name' => $tag_name, 'tag_score' => $tag_score ];
                $count++;
            }
        }
        return $tag_list_format;
    }

    /**
     * 获取格言
     */
    public function getMotto()
    {
        $motto_model = new MottoModel();
        $motto = $motto_model->getMotto();
        return $motto;
    }

    /**
     * 获取今日推荐
     */
    public function getHotArticle()
    {
        $article_model = new ArticleModel(); 
        $hot_article_detail = $article_model->getHotArticle();
        return $hot_article_detail;
    }
}
