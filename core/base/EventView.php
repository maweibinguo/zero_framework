<?php
namespace core\base;

use Phalcon\Events\Event;
use Phalcon\Mvc\View;

class EventView extends Components
{
    public function notFoundView(Event $event, View $view)
    {
        var_dump('没找到对应模板文件:'.$view->getActiveRenderPath());die();
    }
}
