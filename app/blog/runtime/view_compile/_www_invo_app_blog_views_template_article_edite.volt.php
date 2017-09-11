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
    <label for="title" class="col-sm-1">文章标题:</label>
    <div class="col-sm-7">
      <input type="text" value="<?php if( isset($article_detail) && $article_detail['title']){ echo $article_detail['title'];}?>"  class="form-control" id="title" placeholder='文章标题'>
    </div>
  </div>

  <div class="form-group">
    <label for="tag" class="col-sm-1">文章标签:</label>
    <div class="col-sm-7">
      <input type="text" class="form-control" value="<?php if(isset($article_detail) && $article_detail['tag']) { echo $article_detail['tag']; }?>" id="tag" placeholder="文章标签，用逗号隔开">
    </div>
  </div>

  <div class="form-group">
    <label for="category" class="col-sm-1">文章类别:</label>
    <div class="col-sm-7">
        <select class="form-control" name="category" id="category">
            <option value=""/>--请选额--</option>
            <?php foreach($category_list as $value => $name) { ?>
                <option value='<?php echo $value;?>'><?php echo $name;?></option>
            <?php }?>
        </select>
    </div>
  </div>

  <div class="form-group">
    <label for="head_image" class="col-sm-1">
        文章头图:
    </label>
    <div class="col-sm-7 col-md-3">
        <div class="thumbnail">
            <img src="<?php if(isset($article_detail['headimage'])) {echo $article_detail['headimage'];} else {echo '/images/excerpt.jpg';}?>" class="default_image" alt="通用的占位符缩略图" style='width:220;height:150px;'>
            <div class="caption">
                <p class='text-center'>
                    <input type="file" name="headimageinput"  id="openview" style="display:none;"/>
                    <input type="hidden" name="headimage"  id="headimage" value='<?php if(isset($article_detail['headimage'])) {echo $article_detail['headimage'];} else { echo '/images/excerpt.jpg'; } ?>'/>
                    <a href="javascript:void(0);" class="btn btn-primary upload-button" role="button">
                        上传
                    </a> 
                </p>
            </div>
        </div>
    </div>
  </div>

  <div class="form-group">
    <label for="test-editormd" class="col-sm-1">
        文章内容:
    </label>
      <div id="test-editormd">
        <textarea style="display:none;"><?php if(isset($article_detail) && $article_detail['mdcontent']){ echo $article_detail['mdcontent'];} ?></textarea>
      </div>
  </div>

  <div class="form-group">
    <div class="col-sm-3 pull-left">
        <button type="button" class="btn btn-primary btn-sm submit" status=0 >保存为草稿</button>
    </div>
    <div class="col-sm-3 pull-left">
        <button type="button" class="btn btn-primary btn-sm submit" status=1>发表</button>
    </div>
  </div>

</form>
