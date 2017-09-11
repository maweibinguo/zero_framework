<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;
use app\blog\validator\Article as ArticleValidator;
use core\base\Log;

class ArticleController extends BaseController
{
    public $page_js = [
                        '/js/editormd/editormd.min.js',
                        '/js/layer/layer/layer.js',
                        '/js/validate/article.js?sf'
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
            if($this->request->isPost()) {
                try{
                    $post_data = $this->request->getPost();
                    $article_validator = new ArticleValidator();
                    $add_article_validator = $article_validator->getAddValidator();
                    $messages_list = $add_article_validator->validate($post_data);
                    if(count($messages_list)) {
                        foreach($messages_list as $message_item) {
                            $message = $message_item->getMessage();
                            $error_message = Log::getErrorMessage($message, __CLASS__, __METHOD__, __LINE__);
                            throw new \Exception($error_message);
                        }
                    }
                    if(empty($post_data['article_id'])) {
                        $error_message = Log::getErrorMessage('文章编号不正确', __CLASS__, __METHOD__, __LINE__);
                        throw new \Exception($error_message);
                    }

                    /* 开始修改文章 */
                    $article_service = new ArticleService(); 
                    $post_data['mdcontent'] = base64_encode( $post_data['mdcontent']);
                    $post_data['htmlcontent'] = base64_encode( $post_data['htmlcontent']);
                    $post_data['add_time'] = time();
                    $article_service->modifyArticle($post_data);
                    $this->responseSuccess('文章修改成功', ['article_id' => $post_data['article_id']]);
                } catch(\Exception $e) {
                    $error_message = $e->getMessage();
                    Log::getInstance()->info($error_message);
                    $this->responseFailed("修改文章失败");
                }
            } else {
                try{
                    //修改添加ajaxuploadjs
                    $this->page_js[] = '/js/ajaxfileupload.js';
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
                    Log::getInstance()->info($error_message);
                    $this->flashSession->error('你要编辑的文章不存在啊！臣妾也很无奈。。。'); 
                    return $this->response->redirect('error/notFound');
                }
            }
    }

    /**
     * 保存文章
     */
    public function createAction()
    {
        if($this->request->isPost()) {
            try{
                /* 接受参数并校验 */
                $post_data = $this->request->getPost();
                $article_validator = new ArticleValidator();
                $add_article_validator = $article_validator->getAddValidator();
                $messages_list = $add_article_validator->validate($post_data);
                if(count($messages_list)) {
                    foreach($messages_list as $message_item) {
                        $message = $message_item->getMessage();
                        $error_message = Log::getErrorMessage($message, __CLASS__, __METHOD__, __LINE__);
                        throw new \Exception($error_message);
                    }
                }

                /* 开始添加文章 */
                $article_service = new ArticleService();
                $post_data['mdcontent'] = base64_encode($post_data['mdcontent']);
                $post_data['htmlcontent'] = base64_encode($post_data['htmlcontent']);
                $article_id= $article_service->addAritcle($post_data);

                /* 输出返回结果 */
                $this->responseSuccess('文章添加成功', ['article_id' => $article_id]);
            } catch (\Exception $e) {
                $error_message = $e->getMessage();
                Log::getInstance()->info($error_message);
                $this->flashSession->error('创建文章失败'); 
                return $this->response->redirect('error/notFound');
            }
        } else {
            //修改添加ajaxuploadjs
            $this->page_js[] = '/js/ajaxfileupload.js';
            $this->title = $this->title . '-编辑文章页面';
            $this->view->pick('article/edite');
        }
    }

    /**
     * 获取文章详情
     */
    public function viewAction()
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
            $article_id = $this->request->get('article_id');
            $article_service = new ArticleService();

            //增加页面浏览量
            $article_service->incrViewNumber($article_id);

            //获取文章数据
            $article_detail = $article_service->getArticleDetail($article_id);
            $article_detail['tag_list'] = explode(',', $article_detail['tag']);
            $this->title = $this->title . '-' . $article_detail['title'];

            //base64反解出来
            $article_detail['htmlcontent'] = base64_decode($article_detail['htmlcontent']);
            $article_detail['mdcontent'] = base64_decode($article_detail['mdcontent']);
            $this->view->setVar('article_detail', $article_detail);
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->flashSession->error('尴尬了，该文章已经飞走了'); 
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

    /**
     * 删除文章
     */
    public function deleteAction()
    {
        try{
            if($this->request->isPost()) {
                $article_id = $this->request->getPost('article_id');
                $article_service = new ArticleService();
                $article_service->deleteTargetArticle($article_id);
                $this->responseSuccess('删除成功', []);
            } else {
                throw new \Exception('请求方式不正确');
            }
        } catch(\Exception $e) {
            $error_message = $e->getMessage();
            Log::getInstance()->info($error_message);
            $this->responseFailed('尴尬了，文章删除失败', []);
        }
    }
}
