<div class="side-body">
                    <div class="page-title">
                        <!--span class="title">Table</span-->
                        <div class="description">文章管理 >> 文章列表</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="card">
                                <div class="card-header">

                                    <div class="card-title">
                                    <div class="title">文章列表</div>
                                    </div>
                                </div>
                                <div class="card-body">
                                        <table class="table table-bordered">
                                        <thead>
                                            <tr class="info text-center" style="text-align:center;">
                                                <th>文章编号</th>
                                                <th>文章标题</th>
                                                <th>文章类别</th>
                                                <th>文章状态</th>
                                                <th>文章标签</th>
                                                <th>添加时间</th>
                                                <th>操作</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                        <?php foreach($article_list as $article_key => $article_item) { ?>
                                            <tr class="text-center">
                                                <td><?php echo $article_item['article_id'];  ?></td>
                                                <td><?php echo $article_item['title']; ?></td>
                                                <td><?php echo $category_list[$article_item['category']]; ?></td>
                                                <td><?php echo $article_status_list[$article_item['status']]; ?></td>
                                                <td><?php echo $article_item['tag']; ?></td>
                                                <td><?php echo $article_item['add_time']; ?></td>
                                                <td>
                                                    <?php if($article_item['status'] == 0) {?>
                                                        <span id="show">展示</span>
                                                    <?php } else {?>
                                                        <span id="hidden">隐藏</span>
                                                    <?php } ?>
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
