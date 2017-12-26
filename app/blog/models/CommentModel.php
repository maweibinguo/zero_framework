<?php
/**
 * 文章model
 */
namespace app\blog\models;
use core\base\Log;

class CommentModel extends BaseModel
{
    /* 文章计数keyname */
    const ARTICLE_COMMENT_LIST = 'article:%s:comment';

    /**
     * 保存评论
     * comment_item = [
     *  'user_id' => 1,
     *  'comment' => 'test',
     *  'add_time' => '22017-11-03 12:12:12'
     * ]
     */
    public function addComment($comment_item)
    {
        $article_item = explode(':', $comment_item['article_id']);
        $article_id = end($article_item);
        $article_comment_key_name = $this->getKeyName([static::ARTICLE_COMMENT_LIST, $article_id]);
        $result = static::$redis->rPush($article_comment_key_name, json_encode($comment_item));
        if($result === false) {
            $error_message = Log::getErrorMessage('保存评论失败', __CLASS__, __METHOD__, __LINE__);
            throw new \Exception($error_message);
        }
        return $article_comment_key_name;
    }

    /**
     * 获取评论
     */
    public function getCommnetList($article_id, $start, $end)
    {
        $article_item = explode(':', $article_id);
        $article_id = end($article_item);
        $article_comment_key_name = $this->getKeyName([static::ARTICLE_COMMENT_LIST, $article_id]);
        $comment_list = static::$redis->lRange($article_comment_key_name, $start, $end);
        return $comment_list;
    }

    /**
     * 获取评论的长度
     */
    public function getCommentLength($article_id)
    {
        $article_item = explode(':', $article_id);
        $article_id = end($article_item);
        $article_comment_key_name = $this->getKeyName([static::ARTICLE_COMMENT_LIST, $article_id]);
        $comment_number = static::$redis->lLen($article_comment_key_name);
        return $comment_number;
    }
}
