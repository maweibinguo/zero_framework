<?php
namespace app\blog\controllers;

class PostController extends BaseController
{
    public function zeroAction()
    {
        var_dump($this->view->setLayout('post'));
    }
}
