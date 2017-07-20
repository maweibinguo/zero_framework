<?php
namespace core\mq;

abstract class Mq extends \Core\Components
{
    abstract public function push($body, $queue, $exchange, $properties);
    abstract public function pull($callback, $queue, $exchange, $properties);
    abstract public function call($body, $queue, $properties);
    abstract public function get($queue, $exchange, $properties);
}
