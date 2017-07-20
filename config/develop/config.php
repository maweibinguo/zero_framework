<?php
return [
    //默认的日志channel
    'default_channel_name' => 'lago_channel',

    //数据库
    'database'             => [
                                    'host'      => '127.0.0.1',
                                    'username'  => 'root',
                                    'password'  => '123qweasd',
                                    'dbname'    => 'recommend',
                                    'options'   => [
                                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                                        \PDO::ATTR_PERSISTENT => true
                                    ]
                                ],

    //redis
    'redis'                => [
                                'host'    => '127.0.0.1',
                                'port'    => '6379',
                                'timeout' => 3
                              ],

    //rabbitmq
    'mq'                   => [
                                'env_prefix' => '',
                                'host' => '127.0.0.1',
                                'port' => '5672',
                                'user' => 'guest',
                                'password' => 'guest',
                                'vhost' => '/',
                                'keepalive' => false,
                                'heartbeat' => 0,
                                'log_path' => '/tmp/runtime/logs/mqlog/', 
                              ]
];
