<div class="side-body">
    <div class="page-title">
        <!--span class="title">Form UI Kits</span-->
        <div class="description">轮播图管理 >> 添加轮播图</div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="title">首页轮播图</div>
                    </div>
                </div>
                <div class="card-body">
                        <input type='hidden' name="pircutre_id" value="<?php if(isset($picture_id)) { echo $picture_id; } ?>" id="picture_id"/>
                        <div class="form-group">
                            <label for="picturename">轮播图名称</label>
                            <input type="text" value="<?php if(isset($picture_detail['picturename'])) { echo $picture_detail['picturename']; } ?>" name="picturename" class="form-control" id="picturename" placeholder="轮播图名称">
                        </div>
                        <div class="form-group">
                            <label for="targeturl">轮播图跳转链接</label>
                            <input type="text" value="<?php if(isset($picture_detail['targeturl'])) { echo $picture_detail['targeturl']; } ?>" name="targeturl" class="form-control" id="targeturl" placeholder="轮播图跳转链接">
                        </div>
                        <div class="form-group">
                            <label for="picturestatus">轮播图状态</label>
                            <?php if(isset($picture_detail['status'])) { ?>
                            <div>
                                  <div class="radio3 radio-check radio-success radio-inline">
                                    <input type="radio" id="normal" name="status" value="1" <?php if($picture_detail['status'] == '1'){echo "checked='true'";}?> />
                                    <label for="normal">
                                      展示
                                    </label>
                                  </div>
                                  <div class="radio3 radio-check radio-warning radio-inline">
                                    <input type="radio" id="disabled" name="status" value="0" <?php if($picture_detail['status'] == '0'){echo "checked='true'";}?> />
                                    <label for="disabled">
                                      隐藏
                                    </label>
                                  </div>
                            </div>
                            <?php } else { ?>
                            <div>
                                  <div class="radio3 radio-check radio-success radio-inline">
                                    <input type="radio" id="normal" name="status" value="1" checked='true'>
                                    <label for="normal">
                                      展示
                                    </label>
                                  </div>
                                  <div class="radio3 radio-check radio-warning radio-inline">
                                    <input type="radio" id="disabled" name="status" value="0">
                                    <label for="disabled">
                                      隐藏
                                    </label>
                                  </div>
                            </div>

                            <?php } ?>
                        </div>

                        <div class="form-group">
                            <label for="headimage">轮播图片</label>
                            <div class="thumbnail">
                                <img src="<?php if(isset($picture_detail['headimage'])) { echo $picture_detail['headimage']; } ?>" class="default_image" alt="请上传图片"  draggable="false">
                                <div class="caption">
                                    <p class="text-center">
                                        <input type="file" name="headimageinput" id="openview" style="display:none;" />
                                        <input type="hidden" name="headimage" id="headimage" value="<?php if(isset($picture_detail['headimage'])) { echo $picture_detail['headimage']; } ?>" />
                                        <a href="javascript:void(0);" class="btn btn-primary upload-button" role="button" draggable="false">
                                            上传
                                        </a> 
                                    </p>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-default submit" url="<?php if(isset($picture_id)) {echo "/picture/modify"; } else { echo "/picture/add"; }?>">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
