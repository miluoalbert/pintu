<div class="content">
    <h4 class="mtb24 pos-r clr">
        <span><i class="icon-point mr10"></i><span class="va-m">分类管理</span></span>
        <span class="right-btn handle" data-status="1" data-toggle="modal">
            <button type="button" class="btn add-btn"><i class="icon-add"></i>添加分类</button>
        </span>
    </h4>
    <!--  通用表格样式 -->
    <table class="table table-bordered panel-bg panel-table" >
        <thead class="panel-table-title">
        <tr><th>ID</th><th>分类名称</th><th>是否显示</th>
            <th>排序</th><th>添加时间</th><th>操作</th></tr>
        </thead>
        <tbody>
        <?php if (! empty($data)): foreach ($data as $row):?>
            <tr>
                <td><?=$row['id']?></td>
                <td><?=$row['name']?></td>
                <td>
                    <label class="switch-wrapper ">
                        <input type="hidden" value="<?php echo $row['id']?>">
                        <span class="text-switch show_switch <?php echo 1 == $row['is_show'] ? 'switch-change' : ''?>" data-val="<?php echo $row['is_show']?>" data-yes="启用" data-no="禁止"></span>
                        <span class="toggle-btn"></span>
                    </label>
                </td>
                <td><?=$row['sort']?></td>
                <td><?=$row['created_at']?></td>
                <td>
                    <a href="javascript:;" class="link-event mlr10 handle" data-id="<?php echo $row['id']?>" data-status="2">编辑</a>
                    <a href="javascript:;" class="link-event mlr10 delete" data-id="<?php echo $row['id']?>">删除</a>
                </td>
            </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="6">暂无数据</td>
        </tr>
        <?php endif;?>
        </tbody>
    </table>
    <!-- 通用分页 -->
    <?= $page?>
</div>

<!-- 模态框（Modal） -->
<div class="modal fade modal-normal" id="InfoModal" tabindex="-1" role="dialog" data-keyboard="false" data-backdrop="static" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="icon-close"></i></button>
                <h4 class="modal-title" id="myModalLabel">分类添加</h4>
            </div>
            <div class="modal-body">
                <form action="" id="submitForm"></form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn confirm-btn btn-middle mr46" id="submitBtn">提 交</button>
                <button type="button" class="btn cancel-btn btn-middle" data-dismiss="modal">取 消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>

<script type="text/html" id="templateTable">
    <table class="table table-bordered panel-table">
        <input type="hidden" name="id" value="{{ data.id }}"/>
        <tbody>
        <tr>
            <td class="table-bg">分类名称</td>
            <td class="wd70p text-left plr25">
                <input type="text" name="name" class="none-bd" placeholder="请输入分类名称" value="{{ data.name }}">
            </td>
        </tr>
        <tr>
            <td class="table-bg">分类排序</td>
            <td class="wd70p text-left plr25">
                <input type="text" name="sort" class="none-bd" placeholder="请输入分类排序" value="{{ data.sort }}">
            </td>
        </tr>
        </tbody>
    </table>
</script>

<script>
    $(function(){
        /* 增加或修改分类 */
        $('.handle').on('click', function(){
            var status = $(this).data('status');
            if (1 == status) {
                var info = {id: 0, name: "", sort: ""};
                var html = template("templateTable", {data: info});
                $('#submitForm').html(html);
                $('#myModalLabel').html('分类添加');
                $('#submitBtn').data('type','1');
                $('#InfoModal').modal();
            } else if(2 == status){
                $.get('/admin/category/get', {'id': $(this).data('id')}, function(result) {
                    hideLoading();
                    if(result.code == 0) {
                        var InfoModal = result.data;
                        var html = template("templateTable", {data:InfoModal});
                        $('#submitForm').html(html);
                        $('#myModalLabel').html('分类编辑');
                        $('#submitBtn').data('type','2');
                        $('#InfoModal').modal();
                    } else {
                        dialog.error(result.msg);
                        return false;
                    }
                });
            }
        });

        /* 表单提交 */
        $('#submitBtn').on('click', function(){
            var type = $(this).data('type');
            if(1 == type){
                var url = '/admin/category/create';
            } else if(2 == type){
                var url = '/admin/category/edit';
            }
            showLoading();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: url,
                data: $('#submitForm').serialize(),
                success: function(response) {
                    hideLoading();
                    if (0 == response.code) {
                        dialog.msg(response.msg);
                        location.reload();
                    } else {
                        dialog.error(response.msg);
                        return false;
                    }
                }
            });
        });

        /* 删除提示 */
        $(".delete").on('click', function(){
            var id = $(this).data('id');
            dialog.success('你确定要删除这个分类吗？',function() {
                showLoading();
                $.post('/admin/category/delete', {'id': id}, function(response) {
                    hideLoading();
                    if(response.code == 0) {
                        dialog.msg(response.msg);
                        window.location.href = window.location.href;
                    } else {
                        dialog.error(response.msg);
                    }
                });
            });
        });

        // 是否显示
        $('.show_switch').on('click',function(){
            var $val = $(this).data('val');
            var $id = $(this).siblings('input').val();
            var $this = $(this);
            showLoading();
            if($val == 0) {
                $.post('/admin/category/show', {'id': $id}, function(response) {
                    hideLoading();
                    if(response.code == 0) {
                        $this.addClass('switch-change').data('val', '1');
                    } else {
                        dialog.error(response.msg);
                        return false;
                    }
                });
            } else {
                $.post('/admin/category/notShow', {'id': $id}, function(response) {
                    hideLoading();
                    if(response.code == 0) {
                        $this.removeClass('switch-change').data('val', '0');
                    } else {
                        dialog.error(response.msg);
                        return false;
                    }
                });
            }
        });
    });
</script>
