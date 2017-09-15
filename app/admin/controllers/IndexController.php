<?php
namespace app\admin\controllers;

use app\admin\service\ArticleService;
use app\admin\models\MottoModel;
use core\base\Log;

class IndexController extends BaseController
{
    /**
     * 加载首页
     */
    public function indexAction()
    {
        try{
            $article_service = new ArticleService();
            $motto_model = new MottoModel();

            //统计正常文章数
            $common_article_number = $article_service->getCommonArticleNumber();

            //统计草稿文章数
            $draft_article_number = $article_service->getDraftArticleNumber();

            //统计格言数量
            $motto_number = $motto_model->getNumber();

            //分配数据
            $this->view->setVar('common_article_number', $common_article_number);
            $this->view->setVar('draft_article_number', $draft_article_number);
            $this->view->setVar('motto_number', $motto_number);
        } catch ( \Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error("页面出错"); 
            return $this->response->redirect('error/notFound');
        }
    }

    /**
     * 加载页面
     */
    public function textAction()
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
               $htmlcontent = strip_tags(base64_decode($article_detail['htmlcontent']));
               $htmlcontent = mb_substr($htmlcontent, 0, 100);
               $article_detail['htmlcontent'] = $htmlcontent;
               unset($article_detail['mdcontent']);
               $article_detail['add_time'] = date('Y-m-d H:i:s', $article_detail['add_time']);
               $article_list[$key] = $article_detail;
           }


           //获取今日推荐
           $hot_article_detail = $article_service->getHotArticle();
           $htmlcontent = strip_tags(base64_decode($hot_article_detail['htmlcontent']));
           $htmlcontent = mb_substr($htmlcontent, 0, 100);
           $hot_article_detail['htmlcontent'] = $htmlcontent;
           unset($hot_article_detail['mdcontent']);
           $hot_article_detail['add_time'] = date('Y-m-d H:i:s', $hot_article_detail['add_time']);

           //获取标签
           $tag_list = $article_service->getTagList();

           //获取日期
           $week_day = $this->getWeekDay();
           $date = date("Y年m月d日") . $week_day;

           //获取格言
           $motto = $article_service->getMotto();

           /* 想页面分配数据 */
           $this->view->setVar('article_list', $article_list);
           $this->view->setVar('condition', $condition);
           $this->view->setVar('tag_list', $tag_list);
           $this->view->setVar('motto', $motto);
           $this->view->setVar('date', $date);
           $this->view->setVar('hot_article_detail', $hot_article_detail);
       } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error("请先登录"); 
            return $this->response->redirect('error/notFound');
       }
    }
}
