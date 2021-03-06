<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="utf-8">
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo $title;?></title>
<meta name="keywords" content="<?php echo $keywords;?>"/>
<meta name="description" content="<?php echo $description; ?>">
<meta name="<?php echo $this->security->getTokenKey();?>" content="<?php echo $this->security->getToken(); ?>" id="token">
<meta name="resubmit" content="<?php echo $this->resubmition->getUniqueValue();?>" id="resubmit">
<?php $this->assets->outputCss('header'); ?>
<link rel="apple-touch-icon-precomposed" href="/images/icon/icon.png">
<!--[if gte IE 9]>
<script src='/js/html5shiv.min.js'></script>
<script src='/js/respond.min.js'></script>
<script src='/js/selectivizr-min.js'></script>
<![endif]-->
<!--[if lt IE 9]>
  <script>window.location.href='upgrade-browser.html';</script>
<![endif]-->
</head>

<body class="user-select">
<header class="header">
  <nav class="navbar navbar-default" id="navbar">
    <div class="container">
      <div class="header-topbar hidden-xs link-border">
        <ul class="site-nav topmenu">
          <li><a href="links.html" rel="nofollow">友情链接</a></li>
          <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" rel="nofollow">关注本站 <span class="caret"></span></a>
            <ul class="dropdown-menu header-topbar-dropdown-menu">

              <li><a data-toggle="modal" data-target="#WeChat" rel="nofollow"><i class="fa fa-weixin"></i> 微信</a></li>
              <li><a data-toggle="modal" data-target="#WeChat" rel="nofollow"><i class="fa fa-github"></i> git</a></li>
              <!--li><a href="#" rel="nofollow"><i class="fa fa-weibo"></i> 微博</a></li-->
              <!--li><a data-toggle="modal" data-target="#areDeveloping" rel="nofollow"><i class="fa fa-rss"></i> RSS</a></li-->
            </ul>
          </li>
        </ul>

        <a data-toggle="modal" data-target="#loginModal" class="login" rel="nofollow">
            <?php if($is_login) {?>
                    Hi,<span style="color:#3399CC"><?php echo $user_data['user_name'];?></span>
                    &nbsp;&nbsp;
                    <a href="/acc/logout"/> 退出登录</a>
            <?php }else{ ?>
                    Hi,请登录
                    <!--/a>&nbsp;&nbsp;<a href="javascript:;" class="register" rel="nofollow">我要注册</a>&nbsp;&nbsp;<a href="" rel="nofollow">找回密码</a-->
            <?php } ?>
        </a>
      </div>
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#header-navbar" aria-expanded="false"> <span class="sr-only"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
        <h1 class="logo hvr-bounce-in"><a href="/index/index" title=""><img src="/images/logo.png" alt="logo"></a></h1>
      </div>
      <div class="collapse navbar-collapse" id="header-navbar">
        <ul class="nav navbar-nav navbar-right">
          <li class="hidden-index active"><a data-cont="insisting首页" href="/index/index">insisting首页</a></li>
          <?php if(isset($category_list)) ?>
          <?php foreach($category_list as $value => $name) { ?>
          <li><a href="/index/index?category=<?php echo $value;?>"><?php echo $name; ?></a></li>
          <?php } ?>
          <?php if($is_login):?>
          <li><a href="/index/index?status=0">草稿列表</a></li>
          <li><a href="/article/create">写文章</a></li>
          <?php endif;?>
        </ul>
        <!--form class="navbar-form visible-xs" action="/Search" method="post">
          <div class="input-group">
            <input type="text" name="keyword" class="form-control" placeholder="请输入关键字" maxlength="20" autocomplete="off">
            <span class="input-group-btn">
            <button class="btn btn-default btn-search" name="search" type="submit">搜索</button>
            </span> </div>
        </form-->
      </div>
    </div>
  </nav>
</header>
<section class="container">
    {{ content() }}
</section>
<footer class="footer">
  <div class="container">
    <p>&copy; 2016 <a href="">ylsat.com</a> &nbsp; <a href="http://www.miitbeian.gov.cn/" target="_blank" rel="nofollow">豫ICP备15002365号-1</a> &nbsp; <a href="sitemap.xml" target="_blank" class="sitemap">网站地图</a></p>
  </div>
  <div id="gotop"><a class="gotop"></a></div>
</footer>
<!--登录注册模态框-->
<div class="modal fade user-select" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="/acc/login" method="post" id='login_form'>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="loginModalLabel">登录</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="loginModalUserNmae">用户名</label>
            <input type="text" class="form-control" id="user_name" placeholder="请输入用户名" autofocus maxlength="15" name="user_name" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label for="loginModalUserPwd">密码</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="请输入密码" maxlength="18" autocomplete="off" required>
          </div>

          <div class="form-group">
            <label for="captcha">验证码</label>
            <input type="text" class="form-control" id="captcha" name="captcha" width="30" placeholder="请输入验证码" maxlength="5" autocomplete="off" required>
            <img src="/api/captcha" alt="验证码" class="img_captcha" style="cursor:pointer;"/>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="submit" class="btn btn-primary">登录</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!--登录注册模态框-->
<!--div class="modal fade user-select" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="/acc/register" method="post" id='register_form'>
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="registerModalLabel">注册</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="registerModalUserNmae">用户名</label>
            <input type="text" class="form-control" id="registerModalUserNmae" placeholder="请输入用户名" autofocus maxlength="15" name="user_name" autocomplete="off" required>
          </div>
          <div class="form-group">
            <label for="registerModalUserPwd">密码</label>
            <input type="password" class="form-control" id="registerModalUserPwd" name="password" placeholder="请输入密码" maxlength="18" autocomplete="off" required>
          </div>

          <div class="form-group">
            <label for="registerCaptcha">验证码</label>
            <input type="text" class="form-control" id="registerCaptcha" name="captcha" width="30" placeholder="请输入验证码" maxlength="5" autocomplete="off" required>
            <img src="/api/captcha" alt="验证码" class="img_captcha" style="cursor:pointer;"/>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
          <button type="submit" class="btn btn-primary">注册</button>
        </div>
      </form>
    </div>
  </div>
</div-->
<?php $this->assets->outputJs('footer'); ?>
</body>
</html>
