<?php
namespace app\admin\controllers;

use app\admin\service\ArticleService;
use app\admin\validator\Article as ArticleValidator;
use core\base\Log;

class ArticleController extends BaseController
{

    /**
     * 获取文章列表
     */
    public function indexAction()
    {
       try{
           /* 获取分页数 */
           $now_number = $this->request->get('now_number'); 
           $now_number = (int)$now_number > 0 ? $now_number : 1;
           $page_size = 10;
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
           $category = $this->request->get('category');
           if( !empty($category) ) {
               $condition['category'] = $category;
           }
           $status = $this->request->get('status');
           if( isset($status) && $status == 0 ) {
               $is_login = $this->isLogin();
               if($is_login === false) {
                    throw new \Exception('请先登录');     
               }
               $condition['status'] = $status;
           }

           /* 获取分页总数 */
           $article_service = new ArticleService();
           $data_list = $article_service->getArticleList($condition);
           $total_page_number = ceil($data_list['article_number'] / $page_size);
           $condition['total_page_number'] = $total_page_number;

           /* 获取文章列表 */
           $article_list = $data_list['article_list'];
           foreach($article_list as $key => $article_id) {
               $article_detail = $article_service->getArticleDetail($article_id);
               unset($article_detail['htmlcontent']);
               unset($article_detail['mdcontent']);
               $article_detail['add_time'] = date('Y-m-d H:i:s', $article_detail['add_time']);
               $article_list[$key] = $article_detail;
           }

           //获取标签
           $tag_list = $article_service->getTagList();

           /* 想页面分配数据 */
           $this->view->setVar('article_list', $article_list);
           $this->view->setVar('condition', $condition);
           $this->view->setVar('tag_list', $tag_list);
       } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error("请先登录"); 
            return $this->response->redirect('error/notFound');
       }

        
    }



    /****************************************** 由于文章的相关功能在前台几乎都能搞定这里功能暂不开发*******************************/
}
