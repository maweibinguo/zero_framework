<div class="content-wrap">
<div class="content">
  <!-- 文章标题 -->
  <header class="article-header">
    <h1 class="article-title"><?php echo $article_detail['title'];?></h1>
    <div class="article-meta"> <span class="item article-meta-time">
      <time class="time" data-toggle="tooltip" data-placement="bottom" title="时间：<?php echo date('Y-m-d H:i:s', $article_detail['add_time']) ?>"><i class="glyphicon glyphicon-time"></i> 2016-1-4 10:29:39</time>
      </span> 
    </div>
  </header>

  <!-- 文章内容 -->
  <article class="article-content">
    <?php var_dump($article_detail);die();?>
    <?php echo $article_detail['htmlcontent']; ?>
  </article>

  <!-- 为文章标签 -->  
  <div class="article-tags">标签：
        <?php foreach($article_detail['tag_list'] as $tag_name) {?>
            <a href="" rel="tag"><?php echo $tag_name;?></a>
        <?php }?>
  </div>

  <div class="title" id="comment">
    <h3>评论 <small>抢沙发</small></h3>
  </div>
  <!--<div id="respond">
    <div class="comment-signarea">
      <h3 class="text-muted">评论前必须登录！</h3>
      <p> <a href="javascript:;" class="btn btn-primary login" rel="nofollow">立即登录</a> &nbsp; <a href="javascript:;" class="btn btn-default register" rel="nofollow">注册</a> </p>
      <h3 class="text-muted">当前文章禁止评论</h3>
    </div>
  </div>-->
  <div id="respond">
    <form action="" method="post" id="comment-form">
      <div class="comment">
        <div class="comment-title"><img class="avatar" src="images/icon/icon.png" alt="" /></div>
        <div class="comment-box">
          <textarea placeholder="您的评论可以一针见血" name="comment" id="comment-textarea" cols="100%" rows="3" tabindex="1" ></textarea>
          <div class="comment-ctrl"> <span class="emotion"><img src="images/face/5.png" width="20" height="20" alt="" />表情</span>
            <div class="comment-prompt"> <i class="fa fa-spin fa-circle-o-notch"></i> <span class="comment-prompt-text"></span> </div>
            <input type="hidden" value="1" class="articleid" />
            <button type="submit" name="comment-submit" id="comment-submit" tabindex="5" articleid="1">评论</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <div id="postcomments">
    <ol class="commentlist">
      <li class="comment-content"><span class="comment-f">#1</span>
        <div class="comment-avatar"><img class="avatar" src="images/icon/icon.png" alt="" /></div>
        <div class="comment-main">
          <p>来自<span class="address">河南郑州</span>的用户<span class="time">(2016-01-06)</span><br />
            这是匿名评论的内容这是匿名评论的内容，这是匿名评论的内容这是匿名评论的内容这是匿名评论的内容这是匿名评论的内容这是匿名评论的内容这是匿名评论的内容。</p>
        </div>
      </li>
    </ol>
    
    <div class="quotes"><span class="disabled">首页</span><span class="disabled">上一页</span><a class="current">1</a><a href="">2</a><span class="disabled">下一页</span><span class="disabled">尾页</span></div>
  </div>
</div>
</div>
