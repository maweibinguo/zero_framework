<?php
/**
 * 封装了文章的相关业务逻辑
 */
namespace app\blog\service;

use core\base\Components;
use app\blog\models\CommentModel;
use core\base\Log;

class CommentService extends Components
{
    /**
     * 添加文章
     */
    public function addComment($article_comment)
    {

        //添加文章
        $comment_model = new CommentModel(); 
        $comment_number = $this->getCommentLength($article_comment['article_id']);
        $article_comment['id_num'] = $comment_number + 1;
        $article_comment['add_time'] = date('Y-m-d H:i:s');
        $article_comment_key_name = $comment_model->addComment($article_comment);
        $article_comment['article_comment_key_name'] = $article_comment_key_name;

        //返回结果
        return $article_comment;
    }

    /**
     * 获取文章列表
     */
    public function getCommentList($article_id, $start, $end)
    {
        //评论列表
        $comment_model = new CommentModel(); 
        $article_comment_list = $comment_model->getCommnetList($article_id, $start, $end);
        if(!empty($article_comment_list)) {
            foreach($article_comment_list as $comment_key => $comment_item) {
                $article_comment_list[$comment_key] = json_decode($comment_item, true);
            }
        }

        //返回结果
        return $article_comment_list;
    }

    /**
     * 获取评论的长度
     */
    public function getCommentLength($article_id)
    {
        $comment_model = new CommentModel(); 
        $comment_number = $comment_model->getCommentLength($article_id);
        return $comment_number;
    }
}

