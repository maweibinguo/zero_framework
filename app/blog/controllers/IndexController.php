<?php
namespace app\blog\controllers;

use app\blog\service\ArticleService;

class IndexController extends BaseController
{
    /**
     * 加载页面
     */
    public function indexAction()
    {
       $article_service = new ArticleService();
       $article_service->getArticleList([]);
    }
}
