<div class="side-body">
    <div class="page-title">
        <!--span class="title">Form UI Kits</span-->
        <div class="description">格言管理 >> 今日格言</div>
    </div>

    <?php if(isset($issuccess) && $issuccess == 1) {?>
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert">×</a>
        <strong>Success!</strong>添加成功
    </div>
    <?php } ?>
    <div class="row">
        <div class="col-xs-12">
            <div class="card">
                <form method="post" action="/motto/motto"/>
                <input type="hidden" name="<?php echo $this->security->getTokenKey();?>" value="<?php echo $this->security->getToken(); ?>"/>
                <div class="card-header">
                    <div class="card-title">
                        <div class="title">添加格言</div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <textarea class="form-control" rows="3" name="motto_content"></textarea>
                    </div>
                </div>
                <div class="center-block text-center">
                    <button type="submit" class="btn btn-default">添加</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
