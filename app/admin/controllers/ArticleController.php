<?php
namespace app\admin\controllers;

use app\admin\service\ArticleService;
use app\admin\validator\Article as ArticleValidator;
use core\base\Log;

class ArticleController extends BaseController
{
    /**
     * 页面的js
     */
    public $page_js = [
                        '/js/layer/layer/layer.js',
                        '/js/validate/article.js'
                      ];

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
           if( isset($status) ) {
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

    /**
     * 隐藏文章
     */
    public function hiddenAction()
    {
        try{
            if($this->request->isPost() === false) {
                $this->responseFailed('请求方式不正确');
            }
            $article_id = $this->request->get('article_id');
            if(empty($article_id)) {
                $this->responseFailed('文章编号不正确');
            }
            $article_service = new ArticleService();
            $article_service->hiddenArticle($article_id);
            $this->responseSuccess("操作成功");
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed("操作失败");
        }
    }

    /**
     * 展示文章
     */
    public function showAction()
    {
        try{
            if($this->request->isPost() === false) {
                $this->responseFailed('请求方式不正确');
            }
            $article_id = $this->request->get('article_id');
            if(empty($article_id)) {
                $this->responseFailed('文章编号不正确');
            }
            $article_service = new ArticleService();
            $article_service->showArticle($article_id);
            $this->responseSuccess("操作成功");
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed("操作失败");
        }
    
    }

}
