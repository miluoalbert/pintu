// 当domReady的时候开始初始化
(function(){
    var $WebUpload = function(pictureId,limitNum,serverUrl) {
        this.pictureId = pictureId;
        this.serverUrl = serverUrl;
        this.uploadBtnId = this.pictureId + "Btn";
        this.uploadPreId = this.pictureId + "Pre";
        this.uploadHide = this.pictureId + "Hide";
        //this.uploadUrl = '/user/upload';
        this.fileNumLimit = limitNum;
        this.fileSizeLimit = 2 * 1024 * 1024;
        // 添加的文件数量
        this.fileCount = 0;
        // 添加的文件总大小
        this.fileSize = 0;
        // 优化retina, 在retina下这个值是2
        this.ratio = window.devicePixelRatio || 1;
        // 缩略图大小
        this.thumbnailWidth = 120;
        this.thumbnailHeight = 90;
        // 可能有pedding, ready, uploading, confirm, done.
        this.state = 'pedding';
        // 所有文件的进度信息，key为file id
        this.percentages = {};
        this.name = '';
        this.webuploader = '';
    };
    $WebUpload.prototype = {
        /**
         * 初始化webUploader
         */
        init : function() {
            webuploader = this.create();
            this.bindEvent(webuploader);
            return webuploader;
        },
        create: function() {
            var webUploader = WebUploader.create({
                /**************************************************** 重要参数 ****************************************************/
                auto: true,                                //是否自动上传（true是，false否）
                swf: './plug-ins/Uploader.swf',                      //flash文件地址
                server: this.serverUrl,                     //上传访问的地址
                /*formData: {act: 'ad_image'}, */            //每次请求附带的参数
                pick: {
                    id: '#' + this.uploadBtnId
                },   //定义选择文件的按钮
                /**************************************************** 其他参数 ****************************************************/
                disableGlobalDnd: false,  // 禁掉全局的拖拽功能。这样不会出现图片拖进页面的时候，把图片打开。
                accept: {                // 指定可以上传那些类型的图片

                    title: 'Images',
                    extensions: 'jpg,jpeg,png,gif,bmp',
                    mimeTypes: 'image/jpg,image/jpeg,image/png,image/gif,image/bmp'
                },
                compress:false, //是否压缩图片,默认true
                thumb:{
                     // 图片质量，只有type为`image/jpeg`的时候才有效。
                     quality: 80,
                     // 是否允许放大，如果想要生成小图的时候不失真，此选项应该设置为false.
                     allowMagnify: false,
                     // 是否允许裁剪。
                     crop: false,
                     // 为空的话则保留原有图片格式。
                     // 否则强制转换成指定的类型。
                     type: ''
                 },
                fileNumLimit: this.fileNumLimit, //最大上传数量，（验证文件总数量, 超出则不允许加入队列）。
                //fileSizeLimit: this.fileSizeLimit  // 验证文件总大小是否超出限制, 超出则不允许加入队列
                fileSingleSizeLimit:this.fileSizeLimit    // 验证单个文件大小是否超出限制, 超出则不允许加入队列
            });
            return webUploader;
        },
        bindEvent :function(bindedObj)  {
            var _this = this;
            /* 文件加入队列 */
            bindedObj.on('fileQueued',function(file){
                _this.addFile(file,bindedObj);
            });
            // 当文件被移除队列后触发。
            bindedObj.on('fileDequeued',function (file) {

                if (!_this.fileCount) {
                    /*_this.setState('pedding');*/
                }
                _this.removeFile(file);

            });
            // 上传过程中....
            bindedObj.on('uploadProgress',function (file, percentage) {
                 var $li = $('#' + file.id);
                 $li.children('.imgWrap').html('<img class="img-loading" src="/static/js/layer/theme/default/loading-1.gif">');
             });
            // 文件上传成功
            bindedObj.on( 'uploadSuccess', function( file,data ) {
                //console.log(file);
                //console.log(data.data);
                var data = data.data;
               // console.log(data);
                console.log(bindedObj.getFiles('error'));
                 var $content = '<input type="hidden" name="'+ _this.pictureId +'Val[]" value="'+data.img_path+'">'+
                                '<img class="img-thumb" src="'+ data.img_url +'">';

                 $('#'+file.id).children('.imgWrap').html($content);

             });

            // 文件上传失败，显示上传出错
            bindedObj.on( 'uploadError', function( file ) {
                //_this.setState('finish');
                //console.log(bindedObj.getFiles('error'));
                bindedObj.removeFile( file,true );
                //console.log(bindedObj.getFiles('error'));
                dialog.error('上传失败，请重新尝试',function(index){
                    $('#'+file.id).remove();
                    _this.fileCount--;
                    if(_this.fileCount == 0){
                        $('input[name=' + this.uploadHide + ']').val('');
                    }
                    if (_this.fileCount < _this.fileNumLimit ) {
                        $('#'+_this.uploadBtnId).show();
                    }
                    layer.close(index);
                })
            });


            // 其他错误
            bindedObj.on('error', function(type) {
                if ("Q_EXCEED_SIZE_LIMIT" == type) {
                    dialog.error("文件大小超出了限制");
                } else if ("Q_TYPE_DENIED" == type) {
                    dialog.error("文件类型不满足");
                } else if ("Q_EXCEED_NUM_LIMIT" == type) {
                    dialog.error("上传数量超过限制");
                } else if ("F_DUPLICATE" == type) {
                    dialog.error("图片选择重复");
                } else if("Q_EXCEED_NUM_LIMIT") {
                    dialog.error("上传文件大小超出限制");
                }else{
                    dialog.error("上传过程中出错");
                }
            });
            this.closeUploader(bindedObj);
        },
        closeUploader:function(){  /* 关闭上传框窗口后恢复上传框初始状态 */
            // 移除所有缩略图并将上传文件移出上传序列
            for (var i = 0; i < webuploader.getFiles().length; i++) {
                // 将图片从上传序列移除
                webuploader.removeFile(webuploader.getFiles()[i]);
                // 将图片从缩略图容器移除
                var $li = $('#' + webuploader.getFiles()[i].id);
                $li.off().remove();
            }

            this.setState('pedding');

            // 重置文件总个数和总大小
            this.fileCount = 0;
            this.fileSize = 0;
            // 重置uploader，目前只重置了文件队列
            webuploader.reset();
            // 更新状态等，重新计算文件总个数和总大小
            //updateStatus();
        },
        /* 增加文件 */
        addFile:function(file,uploader){
            //console.log(file);
            var _this= this;
            var text;
            var $li = $('<li id="' + file.id + '">' +
                    '<p class="imgWrap mg0"></p>' +
                    '</li>'),
                $btns = $('<div class="file-panel">' +
                    '<span class="cancel">删除</span>' +
                    '<span class="rotateRight" style="display:none">向右旋转</span>' +
                    '<span class="rotateLeft" style="display:none">向左旋转</span></div>').appendTo($li),

                $wrap = $li.find('p.imgWrap'),
                $info = $('<p class="error"></p>'),

                showError = function (code) {
                    switch (code) {
                        case 'exceed_size':
                            text = '文件大小超出';
                            break;

                        case 'interrupt':
                            text = '上传暂停';
                            break;

                        default:
                            text = '上传失败，请重试';
                            break;
                    }

                    $info.text(text).appendTo($li);
                };

            _this.fileCount++;
            _this.fileSize += file.size;
            //console.log(_this.fileCount)
            if (_this.fileCount === _this.fileNumLimit ) {
                $('#'+_this.uploadBtnId).hide();
            }

            if(_this.fileCount == 1){
                $('input[name=' + this.uploadHide + ']').val('1');
            }

            $li.on('mouseenter', function () {
                $btns.stop().animate({height: 30});
            });

            $li.on('mouseleave', function () {
                $btns.stop().animate({height: 0});
            });

            $btns.on('click', 'span', function () {
                var index = $(this).index(),
                    deg;

                switch (index) {
                    case 0:
                        // 把当前文件移出当前对列
                        uploader.removeFile(file);
                        return;

                    case 1:
                        file.rotation += 90;
                        break;

                    case 2:
                        file.rotation -= 90;
                        break;
                }

                if (supportTransition) {
                    deg = 'rotate(' + file.rotation + 'deg)';
                    $wrap.css({
                        '-webkit-transform': deg,
                        '-mos-transform': deg,
                        '-o-transform': deg,
                        'transform': deg
                    });
                } else {
                    $wrap.css('filter', 'progid:DXImageTransform.Microsoft.BasicImage(rotation=' + (~~((file.rotation / 90) % 4 + 4) % 4) + ')');
                }


            });
            //console.log(file);
            if(file.status && file.status == "complete"){
                var $content = '<input type="hidden" name="'+ _this.pictureId +'Val[]" value="'+file.img_path+'">'+
                                '<img class="img-thumb" src="'+ file.img_url +'">';
                $wrap.html($content);
            }
            $li.appendTo($('#'+this.uploadPreId));
        },
        /* 移除文件 */
        removeFile:function(file){
            var _this = this;
            _this.fileCount--;
            _this.fileSize -= file.size;
            var $li = $('#' + file.id);
            $li.off().find('.file-panel').off().end().remove();
            //console.log(this.fileCount)
            if(this.fileCount < this.fileNumLimit){
                $('#'+this.uploadBtnId).show();
            }
            if(this.fileCount == 0){
                $('input[name=' + this.uploadHide + ']').val('');
            }
        },
        /* 状态 */
        setState:function(val){
            var file, stats;

            if (val === this.state) {
                return;
            }

            /* $upload上传按钮 */
            $upload.removeClass('state-' + state);
            $upload.addClass('state-' + val);
            state = val;

            switch (state) {
                case 'pedding':
                    $placeHolder.removeClass('element-invisible');
                    $queue.hide();
                    //$queue.show();
                    $statusBar.addClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'ready':
                    $placeHolder.addClass('element-invisible');
                    $queue.show();
                    $statusBar.removeClass('element-invisible');
                    uploader.refresh();
                    break;

                case 'uploading':
                    $progress.show();
                    $upload.text('暂停上传');
                    break;

                case 'paused':
                    $progress.show();
                    $upload.text('继续上传');
                    break;

                case 'confirm':
                    $progress.hide();
                    //$queue.show();
                    $upload.text('开始上传');

                    stats = uploader.getStats();
                    if (stats.successNum && !stats.uploadFailNum) {
                        setState('finish');
                        return;
                    }
                    break;
                case 'finish':
                    stats = uploader.getStats();
                    if (stats.successNum) {
                        //alert('上传成功');
                    } else {
                        // 没有成功的图片，重设
                        state = 'done';
                        //location.reload();
                    }
                    break;
            }

            this.updateStatus();
        },
        supportTransition:function() {
            var s = document.createElement('p').style,
                r = 'transition' in s ||
                    'WebkitTransition' in s ||
                    'MozTransition' in s ||
                    'msTransition' in s ||
                    'OTransition' in s;
            s = null;
            return r;
        }
    };
    window.$WebUpload = $WebUpload;
})();
