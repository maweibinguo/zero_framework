<?php if(isset($article_list) && is_array($article_list)) :?>
  <?php foreach($article_list as $article_detail) :?>
  <article class="excerpt excerpt-1"><a class="focus" href="/article/view?article_id=<?php echo $article_detail['article_id'];?>" title=""><img class="thumb" data-original="/images/excerpt.jpg" src="/images/excerpt.jpg" alt=""></a>
    <header><a class="cat" href="program">后端程序<i></i></a>
      <h2><a href="/article/view?article_id=<?php echo $article_detail['article_id'];?>" title=""><?php echo $article_detail['title'];?></a></h2>
    </header>
    <p class="meta">
      <time class="time"><i class="glyphicon glyphicon-time"></i><?php echo $article_detail['add_time'];?></time>
      <span class="views"><i class="glyphicon glyphicon-eye-open"></i> 共120人围观</span> 
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
