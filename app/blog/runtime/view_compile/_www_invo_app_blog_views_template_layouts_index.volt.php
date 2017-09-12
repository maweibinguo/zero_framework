<div class="content-wrap">
<div class="content">
  <div class="jumbotron">
    <h1>欢迎访问异清轩博客</h1>
    <p>在这里可以看到前端技术，后端程序，网站内容管理系统等文章，还有我的程序人生！</p>
  </div>
  <div id="focusslide" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
      <li data-target="#focusslide" data-slide-to="0" class="active"></li>
      <li data-target="#focusslide" data-slide-to="1"></li>
      <li data-target="#focusslide" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner" role="listbox">
      <div class="item active"> <a href="" target="_blank"><img src="/images/banner/banner_01.jpg" alt="" class="img-responsive"></a> 
        <!--<div class="carousel-caption"> </div>--> 
      </div>
      <div class="item"> <a href="" target="_blank"><img src="/images/banner/banner_02.jpg" alt="" class="img-responsive"></a> 
        <!--<div class="carousel-caption"> </div>--> 
      </div>
      <div class="item"> <a href="" target="_blank"><img src="/images/banner/banner_03.jpg" alt="" class="img-responsive"></a> 
        <!--<div class="carousel-caption"> </div>--> 
      </div>
    </div>
    <a class="left carousel-control" href="#focusslide" role="button" data-slide="prev" rel="nofollow"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">上一个</span> </a> <a class="right carousel-control" href="#focusslide" role="button" data-slide="next" rel="nofollow"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">下一个</span> </a> </div>
  <article class="excerpt-minic excerpt-minic-index">
    <h2><span class="red">【今日推荐】</span><a href="/article/view?article_id=<?php if(isset($hot_article_detail)) { echo $hot_article_detail['article_id']; } ?>" title="<?php if(isset($hot_article_detail)) { echo $hot_article_detail['title'];  } ?>"> <?php if(isset($hot_article_detail)) { echo $hot_article_detail['title'];  } ?></a></h2>
    <p class="note"> <?php if(isset($hot_article_detail)) { echo $hot_article_detail['htmlcontent'], '......'; } ?> </p>
  </article>
  <div class="title">
    <h3>最新发布</h3>
    <div class="more"></div>
  </div>
    <?php 
        echo $this->getContent();
    ?>
  <nav class="pagination" style="display: none;">
    <ul>
      <li class="next-page"><a  href="/index/index?now_number=<?php echo $condition['now_number'] + 1;?>">下一页</a></li>
      <li><span>共 <span id='total_page_number'><?php echo $condition['total_page_number'];?></span> 页</span></li>
    </ul>
  </nav>
</div>
</div>
<?php
    $this->partial("share/sidebar");
?>