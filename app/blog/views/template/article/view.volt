  <!-- 文章标题 -->
  <header class="article-header">
    <h1 class="article-title"><?php echo $article_detail['title'];?></h1>
    <div class="article-meta"> <span class="item article-meta-time">

      <span class="glyphicon glyphicon-calendar"></span>
      <?php echo date('Y-m-d H:i:s',$article_detail['add_time']);?></time>
      &nbsp;&nbsp;
      <span class="views"><i class="glyphicon glyphicon-eye-open"></i> 共<?php echo $article_detail['article_view_statistics']; ?>人浏览</span>
      <?php if($is_login) :?>
      &nbsp;&nbsp;
      <a href='/article/edite?article_id=<?php echo $article_detail['article_id'];?>'/><span class="glyphicon glyphicon-edit">编辑</span></a>
      &nbsp;&nbsp;
       <a href="javascript:void(0)"  article_id="<?php echo $article_detail['article_id'];?>" id="delete_article"/><span class="glyphicon glyphicon-remove-circle"></span>删除</a>
      <?php endif;?>
      </span> 
    </div>
  </header>

  <!-- 文章内容 -->
  <div id='hidden_article_content' style="display:none;">
    <?php echo $article_detail['mdcontent']; ?>
  </div>
  <article class="article-content" id="article_content">
  </article>

  <!-- 为文章标签 -->  
  <div class="article-tags">标签：
        <?php foreach($article_detail['tag_list'] as $tag_name) {?>
            <a href="" rel="tag"><?php echo $tag_name;?></a>
        <?php }?>
  </div>

  <!--div class="title" id="comment">
    <h3>评论 <small>抢沙发</small></h3>
  </div>
  <div id="respond">
    <div class="comment-signarea">
      <h3 class="text-muted">评论前必须登录！</h3>
      <p> <a href="javascript:;" class="btn btn-primary login" rel="nofollow">立即登录</a> &nbsp; <a href="javascript:;" class="btn btn-default register" rel="nofollow">注册</a> </p>
      <h3 class="text-muted">当前文章禁止评论</h3>
    </div>
  </div>-->

  <div id="respond">
      <div class="comment">
        <div class="comment-title"><img class="avatar" src="/images/icon/icon.png" alt="" /></div>
        <div class="comment-box">
          <textarea placeholder="您的评论可以一针见血" name="comment" id="comment-textarea" cols="100%" rows="3" tabindex="1" ></textarea>
          <div class="comment-ctrl"> <span class="emotion"><img src="/images/face/5.png" width="20" height="20" alt="" />表情</span>
            <div class="comment-prompt"> <i class="fa fa-spin fa-circle-o-notch"></i> <span class="comment-prompt-text"></span> </div>
            <input type="hidden" value="<?php if(isset($article_id)){echo $article_id;}?>" class="articleid" />
            <button type="submit" name="comment-submit" id="comment-submit" tabindex="5" articleid="1">评论</button>
          </div>
        </div>

         <div style="margin-top:10px;">
            <input type="text" class="captcha_comment" style="height:35px;" placeholder="请输入验证码" maxlength="5" autocomplete="off" required="">
            <img src="/api/captcha" style="height:35px;cursor:pointer;border:1px solid gray;" alt="验证码" class="img_captcha" draggable="false">
         </div>

      </div>
  </div>

  <div id="postcomments">
    <ol class="commentlist">
      <?php if(!empty($comment_list)) {?>

          <?php foreach($comment_list as $key => $item) {?>
          <li class="comment-content"><span class="comment-f">#<?php echo $key + 1;?></span>
            <div class="comment-avatar"><img class="avatar" src="/images/icon/icon.png" alt="" /></div>
            <div class="comment-main">
              <p>来自用户<span class="address">zero</span>(<span class="time"><?php echo $item['add_time'];?></span>)<br />
                <span class="content"><?php echo $item['article_comment']; ?></span>
              </p>
            </div>
          </li>
          <?php } ?>

      <?php } else {?>
          <li class="comment-content" style="display:none;"><span class="comment-f"></span>
            <div class="comment-avatar"><img class="avatar" src="/images/icon/icon.png" alt="" /></div>
            <div class="comment-main">
              <p>来自用户<span class="address">zero</span>(<span class="time"></span>)<br />
                <span class="content"></span>
              </p>
            </div>
          </li>
      <?php } ?>
    </ol>
  </div>
