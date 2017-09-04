<?php
return [
    'upload_file' => [
                        'allow_type'        => ["image/jpg", "image/jpeg", "image/gif", "image/png", "mage/bmp", "image/webp"],
                        'allow_max_size'    => 2 * 1024 * 1024 * 1024
                    ],

    'needed_login'  => [
                        'article' => [
                                        'create',
                                        'edite',
                                        'getMdContent'
                                     ]
                       ],

    'check_resubmit' => [
                            'article' => [
                                            'edite',
                                            'create',
                                            'delete'
                                         ]
                        ],

    //保存登录信息的session_name
    'login_session_name' => 'insisting_login',

    //jwt的秘钥
    'jwt' => [  'secure_key' => 'b1e10b5df9cc96729b65f6833ea2f960'  ]
];
