<?php
/**
 * 文章model
 */
namespace app\admin\models;

use core\base\Log;

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
            static::$redis->zIncrBy(static::ARTICLE_TAG, 1, $article_tag);

            $key_name = $this->getKeyName([ static::TAG_OF_ARTICLE, $article_tag ]);
            $result = static::$redis->zAdd($key_name, $article_tag_detail['add_time'], $article_key_name);
            if($result == 0) {
                $error_message = Log::getErrorMessage('添加文章标签失败', __CLASS__, __METHOD__, __LINE__);
                throw new \Exception($error_message);       
            }
        }
    }

    /**
     * 修改文章标签
     */
    public function editeArticleTag($article_detail, $old_article_detail)
    {
        //首先获取原有文章的标签
        $article_tag_str = $old_article_detail['tag'];
        $article_tag_list = explode(',', $article_tag_str);

        //获取本次的标签
        $now_tag_str = $article_detail['tag'];
        $now_tag_list = explode(',', $now_tag_str);

        //需要删除
        $need_deleted_tag = array_diff($article_tag_list, $now_tag_list);
        $need_added_tag = array_diff($now_tag_list, $article_tag_list);
        
        //是否两次状态一致
        $is_same = ($article_detail['status'] == $old_article_detail['status']) ? true : false;
        switch($article_detail['status']) {
            case ArticleModel::ARTICLE_STATUS_DRAFT://草稿
                if($is_same === true) {
                    //do nothing
                } else {
                    foreach($article_tag_list as $tag_name) {//删除原有的标签
                        $this->deleteArticleFromTargetTag($article_detail['article_id'], $tag_name);
                    }
                }
                break;
            case ArticleModel::ARTICLE_STATUS_PUBLIC://正常
                if($is_same === true) {
                    //删除一些标签
                    if($need_deleted_tag) {
                        foreach($need_deleted_tag as $tag_name) {
                            $this->deleteArticleFromTargetTag($article_detail['article_id'], $tag_name);
                        }
                    }

                    //添加一些标签
                    if($need_added_tag) {
                        foreach($need_added_tag as $tag_name) {
                            $this->addArticleToTag($article_detail['article_id'], $tag_name, $article_detail['add_time']);
                        }
                    }
                } else {
                    foreach($now_tag_list as $tag_name) {//删除原有的标签
                        $this->addArticleToTag($article_detail['article_id'], $tag_name, $article_detail['add_time']);
                    }
                }
                break;
        }
    }

    /**
     * 删除指定标签下的文章
     */
    public function deleteArticleFromTargetTag($article_id, $tag_name)
    {
        $new_scores = static::$redis->zIncrBy(static::ARTICLE_TAG, -1, $tag_name);// 将标签的积分下降

        if($new_scores <= 0.0) {
            $deleted_number = static::$redis->zDelete(static::ARTICLE_TAG, $tag_name);//从集合中删除标签
            if($deleted_number <= 0) {
                $error_message = Log::getErrorMessage('从标签集合中删除标签失败', __CLASS__, __METHOD__, __LINE__);
                throw new \Exception($error_message);       
            }
        }
        
        //从标签中删除该文章
        $key_name = $this->getKeyName([ static::TAG_OF_ARTICLE, $tag_name]);
        $result = static::$redis->zDelete($key_name, $article_id);
        if(empty($result)) {
            $error_message = Log::getErrorMessage('删除指定标签下的文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);       
        }
    }

    /**
     * 在指定标签下添加文章
     */
    public function addArticleToTag($article_id, $tag_name, $scores)
    {
        //为指定的标签增加一分
        $result = static::$redis->zIncrBy(static::ARTICLE_TAG, 1, $tag_name);
        if(empty($result)) {
            $error_message = Log::getErrorMessage('删除标签失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);       
        }

        //将文章添加到这个标签下
        $key_name = $this->getKeyName([ static::TAG_OF_ARTICLE, $tag_name]);
        $new_number = static::$redis->zAdd($key_name, $scores, $article_id);
        if($new_number <= 0) {
            $error_message = Log::getErrorMessage('向指定标签下添加文章失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);       
        }
    }

    /**
     * 获取指定标签下的文章数
     */
    public function getArticleListByTag($condition)
    {
        $return_data = [];

        $key_name = $this->getKeyName([ static::TAG_OF_ARTICLE, $condition['tag'] ]);
        $article_number = static::$redis->zCard($key_name);
        $return_data['article_number'] = $article_number;

        $article_list = static::$redis->zRevRange(  $key_name, 
                                                    $condition['start'], 
                                                    $condition['page_size']
                                                      );
        $return_data['article_list'] = $article_list;

        return $return_data;
    }

    /**
     * 获取标签
     */
    public function getTagList()
    {
        $article_tag_model = new ArticleTagModel();
        $tag_list = static::$redis->zRevRange(  static::ARTICLE_TAG,
                                                0,
                                                -1  );
        return $tag_list;
    }
}
