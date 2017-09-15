<div class="side-body">
                    <div class="page-title">
                        <!--span class="title">Table</span-->
                        <div class="description">轮播图管理 >> 轮播图列表</div>

    <?php if($this->flashSession->output()) {?>
    <div class="alert alert-success">
        <a class="close" data-dismiss="alert">×</a>
        <strong>Success!</strong><?php echo $this->flashSession->output();?>
    </div>
    <?php } ?>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card">
                                <div class="card-header">

                                    <div class="card-title">
                                    <div class="title">轮播图列表</div>
                                    </div>
                                </div>
                                <div class="card-body">
                                        <table class="table table-bordered">
                                        <thead>
                                            <tr class="info text-center" style="text-align:center;">
                                                <th>轮播图编号</th>
                                                <th>轮播图名称</th>
                                                <th>跳转地址</th>
                                                <th>轮播图状态</th>
                                                <th>添加时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        <?php foreach($picture_list as $picture_key => $picture_item) { ?>
                                            <tr class="text-center">
                                                <td><?php echo $picture_item['picture_id'];  ?></td>
                                                <td><?php echo $picture_item['picturename']; ?></td>
                                                <td><?php echo $picture_item['targeturl']; ?></td>
                                                <td><?php echo $picture_status_list[$picture_item['status']]; ?></td>
                                                <td><?php echo $picture_item['add_time']; ?></td>
                                                <td>
                                                    <?php if($picture_item['status'] == 0) {?>
                                                        <span id="disable">启用</span>
                                                    <?php } else {?>
                                                        <span id="enable">停用</span>
                                                    <?php } ?>
                                                    &nbsp | &nbsp <a href="/picture/modify?picture_id=<?php echo $picture_item['picture_id']; ?>"/>修改</a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
