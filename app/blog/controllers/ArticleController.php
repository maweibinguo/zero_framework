<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;
use app\blog\validator\Article as ArticleValidator;

class ArticleController extends BaseController
{
    public $page_js = [
                        '/js/editormd.min.js',
                        '/js/layer/layer/layer.js',
                        '/js/validate/article.js'
                      ];

    public $page_css = [
                            '/css/style_editor.css',
                            '/css/editormd.css'
                        ];

    /**
     * 页面加载
     */
    public function editeAction()
    {
        $this->title = $this->title . '-编辑文章页面';
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
            $this->response->error($e->getMessage());
        }
    }

    /**
     * 获取文章详情
     */
    public function getArticleAction()
    {
        $this->page_js = [];
        $article_id = $this->request->get('article_id');
        $article_service = new ArticleService();
        $article_detail = $article_service->getArticleDetail($article_id);
        $article_detail['tag_list'] = explode(',', $article_detail['tag']);
        $this->title = $this->title . '-' . $article_detail['title'];
        $this->view->setVar('article_detail', $article_detail);
    }
}
