<?php if(isset($article_list) && is_array($article_list)) :?>
  <?php foreach($article_list as $article_detail) :?>
  <article class="excerpt excerpt-1"><a class="focus" href="/article/view?article_id=<?php echo $article_detail['article_id'];?>" title=""><img class="thumb" data-original="<?php if(isset($article_detail['headimage'])) {echo $article_detail['headimage'];} else { echo "/images/excerpt.jpg"; }?>" src="<?php if(isset($article_detail['headimage'])) {echo $article_detail['headimage'];} else { echo "/images/excerpt.jpg"; } ?>" alt="文章头图"></a>
    <?php if(isset($article_detail['category']) && !empty($article_detail['category'])) {?>
    <header><a class="cat" href="/index/index?category=<?php if(isset($article_detail['category']) && !empty($article_detail['category'])) { echo $article_detail['category']; } ?> "><?php if( isset($article_detail['category']) && isset($category_list) ) { echo $category_list[$article_detail['category']]; }?><i></i></a>
    <?php }?>
      <h2><a href="/article/view?article_id=<?php echo $article_detail['article_id'];?>" title="<?php echo $article_detail['title'];?>"><?php echo $article_detail['title'];?></a></h2>
    </header>
    <p class="meta">
      <time class="time"><i class="glyphicon glyphicon-time"></i><?php echo $article_detail['add_time'];?></time>
      <span class="views"><i class="glyphicon glyphicon-eye-open"></i> 共<?php echo $article_detail['article_view_statistics'];?>人浏览</span> 

      <span class="glyphicon glyphicon-tag"></span>标签:
      <?php if(isset($article_detail['tag_list'])) { ?>
            <?php foreach($article_detail['tag_list'] as $key_name => $tag_name) {?>
                <a href="/index/index/tag=<?php echo $tag_name; ?>"><?php echo $tag_name;?></a>
            <?php } ?>
      <?php } ?>
      <!--a class="comment" href="article.html#comment"><!--i class="glyphicon glyphicon-comment"></i> 0个不明物体</a-->
    </p>
    <p class="note">
        <?php
            echo $article_detail['htmlcontent'], '......';
        ?>
    </p>
  </article>
  <?php endforeach;?>
<?php endif; ?>
