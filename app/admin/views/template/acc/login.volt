<!DOCTYPE html>
<html>

<head>
    <title>Flat Admin V.2 - Free Bootstrap Admin Templates</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Roboto+Condensed:300,400' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,900' rel='stylesheet' type='text/css'>

    <title><?php echo $title;?></title>
    <meta name="keywords" content="<?php echo $keywords;?>"/>
    <meta name="description" content="<?php echo $description; ?>">
    <meta name="<?php echo $this->security->getTokenKey();?>" content="<?php echo $this->security->getToken(); ?>" id="token">
    <meta name="resubmit" content="<?php echo $this->resubmition->getUniqueValue();?>" id="resubmit">
    <?php $this->assets->outputCss('header'); ?>

</head>

<body class="flat-blue login-page">
    <div class="container">
        <div class="login-box">
            <div>
                <div class="login-form row">
                    <div class="col-sm-12 text-center login-header">
                        <i class="login-logo fa fa-connectdevelop fa-5x"></i>
                        <h4 class="login-title">Insisting 博客管理系统</h4>
                    </div>
                    <div class="col-sm-12">
                        <div class="login-body">
                            <div class="progress hidden" id="login-progress">
                                <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                    Log In...
                                </div>
                            </div>
                            <form id='login_form'>
                                <div class="control">
                                    <input type="text" class="form-control" value=""  placeholder="用户名" name="user_name" id="user_name"/>
                                </div>
                                <div class="control">
                                    <input type="password" class="form-control" value="" placeholder="用户密码" name="password" id="password"/>
                                </div>
                                <div class="control">
                                    <input type="text" class="form-control" value="" placeholder="验证码" name="captcha" id="captcha"/>
                                </div>
                                <div class="text-left">
                                    <img src="/api/captcha" alt="验证码" class="img_captcha" style="cursor:pointer;" draggable="false">
                                </div>
                                <div class="login-button text-center">
                                    <input type="submit" class="btn btn-primary" value="登录">
                                </div>
                            </form>
                        </div>
                        <div class="login-footer">
                            <span class="text-right"><a href="#" class="color-white">Forgot password?</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->assets->outputJs('footer'); ?>
</body>

</html>
