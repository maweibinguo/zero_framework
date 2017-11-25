<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use core\base\Log;
use Gregwar\Captcha\CaptchaBuilder;

class IndexController extends BaseController
{
    public $page_js = [
                       'js/dropload.min.js',
                        ];

    public $page_css = [
                       'css/dropload.css',
                        ];
    /**
     * 加载页面
     */
    public function indexAction()
    {
       try{
           /* 获取分页数 */
           $now_number = 1;
           $page_size = 2;
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
           } else {
               $condition['status'] = 1;
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
               $article_detail['tag_list'] = explode(',', $article_detail['tag']);
               $article_detail['add_time'] = date('Y-m-d H:i:s', $article_detail['add_time']);
               $article_list[$key] = $article_detail;
           }

           //获取轮播图
           $picture_list = file_get_contents('http://blog.insisting.top/api/picture');
           $picture_list = json_decode($picture_list, true);

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
           $this->view->setVar('picture_list', $picture_list);
       } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error("请先登录"); 
            return $this->response->redirect('error/notFound');
       }
    }

    /**
     * 分页获取数据
     */
    public function ajaxAction()
    {
       try{
           /* 获取分页数 */
           $now_number = $this->request->get('now_number'); 
           $now_number = (int)$now_number > 0 ? $now_number : 1;
           $page_size = 2;
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
           } else {
               $condition['status'] = 1;
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
               $article_detail['tag_list'] = explode(',', $article_detail['tag']);
               $article_detail['add_time'] = date('Y-m-d H:i:s', $article_detail['add_time']);
               $article_list[$key] = $article_detail;
           }

            $this->responseSuccess('', $article_list, $is_check = false);
           /* 想页面分配数据 */
       } catch (\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed('获取文章列表失败', [], $is_check = false);
       }
    }
}
