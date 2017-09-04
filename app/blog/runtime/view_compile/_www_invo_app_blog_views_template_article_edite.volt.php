<?php
    if(isset($article_detail) && !empty($article_detail)) {
        $action_url = '/article/edite';
    } else {
        $action_url = '/article/create';
    }
?>
<form class="form-horizontal" role="form" action="<?php echo $action_url;?>">
    <?php if(isset($article_detail) && !empty($article_detail)) {?>
        <input type="hidden" name="article_id" id="article_id" value="<?php echo $article_detail['article_id'];?>"/>
    <?php } ?>
  <div class="form-group">
    <div class="col-sm-3">
      <input type="text" value="<?php if( isset($article_detail) && $article_detail['title']){ echo $article_detail['title'];}?>"  class="form-control" id="title" placeholder='文章标题'>
    </div>
    <div class="col-sm-3">
      <input type="text" class="form-control" value="<?php if(isset($article_detail) && $article_detail['tag']) { echo $article_detail['tag']; }?>" id="tag" placeholder="文章标签，用逗号隔开">
    </div>
    <button type="button" class="btn btn-primary btn-sm submit" style="color:blue" status=0 >保存为草稿</button>
    <button type="button" class="btn btn-primary btn-sm submit" style="color:blue" status=1>发表</button>
  </div>
    <div id="test-editormd">
        <textarea style="display:none;"><?php if(isset($article_detail) && $article_detail['mdcontent']){ echo $article_detail['mdcontent'];} ?></textarea>
    </div>
</form>
