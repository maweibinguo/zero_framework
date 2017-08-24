<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;
use app\blog\validator\Article as ArticleValidator;

class ArticleController extends BaseController
{
    public $page_js = [
                        '/js/editormd/editormd.min.js',
                        '/js/layer/layer/layer.js',
                        '/js/validate/article.js?name=scvf'
                      ];

    public $page_css = [
                            '/css/style_editor.css',
                            '/css/editormd.css'
                        ];

    /**
     * 修改文章
     */
    public function editeAction()
    {
        try{
            $article_id = $this->request->get('article_id');
            $article_service = new ArticleService();
            $article_detail = $article_service->getArticleDetail($article_id);
            $this->title = $this->title . '-' . $article_detail['title'];

            //base64反解出来
            $article_detail['htmlcontent'] = base64_decode($article_detail['htmlcontent']);
            $article_detail['mdcontent'] = base64_decode($article_detail['mdcontent']);
            $article_detail['article_id'] = $article_id;
            $this->view->setVar('article_detail', $article_detail);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            $this->flashSession->success($error_message); 
            return $this->response->redirect('error/notFound');
        }
    }

    /**
     * 页面加载
     */
    public function createAction()
    {
        $this->title = $this->title . '-编辑文章页面';
        $this->view->pick('article/edite');
    }

    /**
     * 保存文章
     */
    public function addArticleAction()
    {
        try{
            /* 接受参数并校验 */
            $post_data = $this->request->getPost();
            $article_validator = new ArticleValidator();
            $add_article_validator = $article_validator->getAddValidator();
            $messages_list = $add_article_validator->validate($post_data);
            if(count($messages_list)) {
                foreach($messages_list as $message_item) {
                    throw new \Exception($message_item->getMessage());
                }
            }

            /* 开始添加文章 */
            $article_service = new ArticleService();
            $post_data['mdcontent'] = base64_encode($post_data['mdcontent']);
            $post_data['htmlcontent'] = base64_encode($post_data['htmlcontent']);
            $add_result = $article_service->addAritcle($post_data);

            /* 输出返回结果 */
            $this->response->success('文章添加成功', $add_result);
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            $this->flashSession->success($error_message); 
            return $this->response->redirect('error/notFound');
        }
    }

    /**
     * 获取文章详情
     */
    public function getArticleAction()
    {
        try{
            $this->page_js = [
                                '/js/editormd/lib/marked.min.js',
                                '/js/editormd/lib/prettify.min.js',
                                '/js/editormd/lib/raphael.min.js',
                                '/js/editormd/lib/underscore.min.js',
                                '/js/editormd/lib/flowchart.min.js',
                                '/js/editormd/lib/jquery.flowchart.min.js',
                                '/js/editormd/editormd.min.js',
                                '/js/editormd/preview.js'
                                ];
            $this->page_css = [
                                '/css/editormd.preview.css'
                                ];
            $article_id = $this->request->get('article_id');
            $article_service = new ArticleService();
            $article_detail = $article_service->getArticleDetail($article_id);
            $article_detail['tag_list'] = explode(',', $article_detail['tag']);
            $this->title = $this->title . '-' . $article_detail['title'];

            //base64反解出来
            $article_detail['htmlcontent'] = base64_decode($article_detail['htmlcontent']);
            $article_detail['mdcontent'] = base64_decode($article_detail['mdcontent']);
            $this->view->setVar('article_detail', $article_detail);
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            $this->flashSession->success($error_message); 
            return $this->response->redirect('error/notFound');
        }
    }

    /**
     * 获取文章的markdown内容
     */
    public function getMdContentAction()
    {
        $article_id = $this->request->get('article_id');
        $article_service = new ArticleService();
        $article_detail = $article_service->getArticleDetail($article_id);

        //base64反解出来
        $article_detail['mdcontent'] = base64_decode($article_detail['mdcontent']);
        $this->view->setVar('article_detail', $article_detail);
        $this->responseSuccess('获取成功', ['mdcontent' => $article_detail['mdcontent']]);
    }
}
