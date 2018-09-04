<div class="content">
    <h4 class="mtb24 pos-r clr">
        <span><i class="icon-point mr10"></i><span class="va-m">图标管理</span></span>
    </h4>
    <div id="icon-files-box"></div>
<!--    <button id="clearAll" style="display:none;" class="btn btn-danger">清除全部</button>-->
</div>
<script src="/resources/js/webuploader.html5only.min.js"></script>
<link rel="stylesheet" type="text/css" href="/resources/css/diyUpload.css">
<script type="text/javascript" src="/resources/js/diyUpload.js"></script>
<script>
    $(function(){
        $('#icon-files-box').diyUpload({
            url: '/admin/upload/batch'
            ,buttonText: '选择图片'
            ,success: function(data){
                if (0 == data.code) {
                    dialog.msg(data.msg);
                    $('#clearAll').show();
                } else {
                    dialog.error(data.msg);
                }
            }
            ,error: function(err) {
                console.info(err);

                return false;
            }
        })
        $('#clearAll').bind('click', function () {
            location.reload();
        });
    });

</script>

