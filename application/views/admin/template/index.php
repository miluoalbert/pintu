<div class="content">
    <h4 class="mtb24 pos-r clr">
        <span><i class="icon-point mr10"></i><span class="va-m">模板管理</span></span>
        </span>
    </h4>
    <!--  通用表格样式 -->
    <table class="table table-bordered panel-bg panel-table" >
        <thead class="panel-table-title">
        <tr><th>ID</th><th>效果图</th><th>模板名称</th>
            <th>是否显示</th>
            <th><a href="<?=$sortBaseUrl . 's=1'?><?=empty($d) ? '&d=1' : ''?>">排序
                    <span class="glyphicon glyphicon-triangle-<?=(empty($d) && $s == 1) ? 'top' : 'bottom'?>"></a></th>
            <th><a href="<?=$sortBaseUrl . 's=2'?><?=empty($d) ? '&d=1' : ''?>">添加时间
                    <span class="glyphicon glyphicon-triangle-<?=(empty($d) && $s == 2) ? 'top' : 'bottom'?>"></span></a></th>
            <th><a href="<?=$sortBaseUrl . 's=3'?><?=empty($d) ? '&d=1' : ''?>">编辑时间
                    <span class="glyphicon glyphicon-triangle-<?=(empty($d) && $s == 3) ? 'top' : 'bottom'?>"></span></a></th>
            <th>操作</th></tr>
        </thead>
        <tbody>
        <?php if (! empty($data)): foreach ($data as $row):?>
            <tr>
                <td><?=$row['id'] ?? ''?></td>
                <td>
                    <span style="margin-right:10px;background-color:#000000;display:inline-block;">
                        <img src="<?=base_url() . $row['img_url'] ?? ''?>" style="height:80px;">
                    </span>
                </td>
                <td><?=$row['name'] ?? ''?></td>
                <td>
                    <label class="switch-wrapper ">
                        <input type="hidden" value="<?php echo $row['id']?>">
                        <span class="text-switch show_switch <?php echo 1 == $row['is_show'] ? 'switch-change' : ''?>" data-val="<?php echo $row['is_show']?>" data-yes="启用" data-no="禁止"></span>
                        <span class="toggle-btn"></span>
                    </label>
                </td>
                <td><?=$row['sort']?></td>
                <td><?=$row['created_at']?></td>
                <td><?=$row['updated_at']?></td>
                <td>
                    <a href="javascript:;" class="link-event mlr10 handle" data-id="<?php echo $row['id']?>" data-status="2">编辑</a>
                    <a href="javascript:;" class="link-event mlr10 delete" data-id="<?php echo $row['id']?>">删除</a>
                </td>
            </tr>
        <?php endforeach; else: ?>
        <tr>
            <td colspan="11">暂无数据</td>
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
                <h4 class="modal-title" id="myModalLabel">模板添加</h4>
            </div>
            <div class="modal-body">
                <form action="">
                    <input type="hidden" name="icon" value="" id="iconInput">
                    <input style="display: none;"  id="iconUpload" type="file" name="icon_file">
                    <input type="hidden" name="image" value="" id="imageInput">
                    <input style="display: none;"  id="imageUpload" type="file" name="image_file">
                </form>
                <form action="" id="submitForm" enctype="multipart/form-data">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn confirm-btn btn-middle mr46" id="submitBtn">提 交</button>
                <button type="button" class="btn cancel-btn btn-middle" data-dismiss="modal">取 消</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<script src="/resources/js/jquery-ui.js"></script>
<script src="/resources/js/jquery.fileupload.js"></script>
<script type="text/html" id="templateTable">
    <table class="table table-bordered panel-table">
        <input type="hidden" name="id" value="{{ data.id }}"/>
        <tbody>
        <tr>
            <td class="table-bg">模板名称</td>
            <td class="wd70p text-left plr25">
                <input type="text" name="name" class="none-bd" placeholder="请输入模板名称" value="{{ data.name }}">
            </td>
        </tr>
        <tr>
            <td class="table-bg">模板排序</td>
            <td class="wd70p text-left plr25">
                <input type="text" name="sort" class="none-bd" placeholder="请输入模板排序" value="{{ data.sort }}">
            </td>
        </tr>
        </tbody>
    </table>
</script>
<script>
    $(function(){
        /* 增加或修改模板 */
        $('.handle').on('click', function(){
            var status = $(this).data('status');
            if (1 == status) {
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/admin/category/getEnable",
                    success: function(data) {
                        hideLoading();
                        if (0 == data.code) {
                            var info = {id: 0, name: "", ename: "", category: data.data, sort: ""};
                            var html = template("templateTable", {data: info});
                            $('#submitForm').html(html);
                            $('#myModalLabel').html('模板添加');
                            $('#submitBtn').data('type','1');
                            $('#InfoModal').modal();
                        } else {
                            dialog.error(data.msg);
                            return false;
                        }
                    }
                });
            } else if(2 == status){
                $.get('/admin/template/get', {'id': $(this).data('id')}, function(result) {
                    hideLoading();
                    if(result.code == 0) {
                        var InfoModal = result.data;
                        var html = template("templateTable", {data:InfoModal});
                        $('#submitForm').html(html);
                        $('#myModalLabel').html('模板编辑');
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
                var url = '/admin/template/create';
            } else if(2 == type){
                var url = '/admin/template/edit';
            }
            showLoading();
            var formData = $('#submitForm').serialize();
            formData = formData + '&icon_url=' + $('#iconInput').val();
            formData = formData + '&icon=' + $('#imageInput').val();
            $.ajax({
                type: "POST",
                dataType: "json",
                url: url,
                data: formData,
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
            dialog.success('你确定要删除这个模板吗？',function() {
                showLoading();
                $.post('/admin/template/delete', {'id': id}, function(response) {
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
                $.post('/admin/template/show', {'id': $id}, function(response) {
                    hideLoading();
                    if(response.code == 0) {
                        $this.addClass('switch-change').data('val', '1');
                    } else {
                        dialog.error(response.msg);
                        return false;
                    }
                });
            } else {
                $.post('/admin/template/notShow', {'id': $id}, function(response) {
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
    $('#iconUpload').fileupload({
        url: '/admin/upload/icon',
        autoUpload: true,//是否自动上传
        dataType: 'json',
        done: function (e, data) {
            if (data.result.code == 0 ) {
                $('#showIconImg').attr('src', data.result.data.url);
                $('#iconInput').val(data.result.data.path);
                $("#delIcon").show();
                $("#addIcon").hide();
            } else {
                dialog.error(data.result.msg);
                return false;
            }
        }
    });
    $('#imageUpload').fileupload({
        url: '/admin/upload/image',
        autoUpload: true,//是否自动上传
        dataType: 'json',
        done: function (e, data) { // 设置文件上传完毕事件的回调函数
            if (data.result.code == 0 ) {
                $('#showImageImg').attr('src', data.result.data.url);
                $('#imageInput').val(data.result.data.path);
                $("#delImage").show();
                $("#addImage").hide();
            } else {
                dialog.error(data.result.msg);
                return false;
            }
        }
    });
    function delIconImg() {
        $('#showIconImg').attr('src', '');
        $('#iconInput').val('');
        $("#delIcon").hide();
        $("#addIcon").show();
    }
    function delImageImg() {
        $('#showImageImg').attr('src', '');
        $('#imageInput').val('');
        $("#delImage").hide();
        $("#addImage").show();
    }
</script>

