<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class IndexController extends BaseController
{
    /**
     * 加载页面
     */
    public function indexAction()
    {
       try{
           /* 获取分页数 */
           $now_number = $this->request->get('now_number'); 
           $now_number = (int)$now_number > 0 ? $now_number : 1;
           $page_size = 5;
           $start = ($now_number - 1) * $page_size;
           $end = $now_number * $page_size - 1;
           $condition['start'] = $start;
           $condition['end'] = $end;
           $condition['now_number'] = $now_number;

           /* 搜索条件 */
           $tag = $this->request->get('tag');
           if( !empty($tag) ) {
               $condition['tag'] = $tag; 
           }

           /* 获取分页总数 */
           $article_service = new ArticleService();
           $data_list = $article_service->getArticleList($condition);
           $total_now_number = ceil($data_list['article_number'] / $page_size);
           $condition['total_now_number'] = $total_now_number;

           /* 获取文章列表 */
           $article_list = $data_list['article_list'];
           foreach($article_list as $key => $article_id) {
               $article_detail = $article_service->getArticleDetail($article_id);
               $content = mb_substr(base64_decode($article_detail['htmlcontent']), 0, 100);
               $article_detail['htmlcontent'] = strip_tags($content);
               unset($article_detail['mdcontent']);
               $article_detail['add_time'] = date('Y-m-d H:i:s', $article_detail['add_time']);
               $article_list[$key] = $article_detail;
           }

           /* 想页面分配数据 */
           $this->view->setVar('article_list', $article_list);
           $this->view->setVar('condition', $condition);
       } catch (\Exception $e) {
            $error_message = $e->getMessage();
            $this->flashSession->error($error_message); 
            return $this->response->redirect('error/notFound');
       }
    }
}
