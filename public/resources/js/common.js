/* 编译字符串 */
function html2Escape(sHtml) {
    return sHtml.replace(/[<>&"]/g,function(c){return {'<':'&lt;','>':'&gt;','&':'&amp;','"':'&quot;'}[c];});
}

/* 清除所有空格 */
function $trimAll(str){
    var str = str.replace(/\s+/g,"");
    return str;
}

/* layer弹窗禁止回车不停加载 */
function preventEnter(){
    document.onkeydown = function(e){
        var ev =document.all ? window.event : e;
        if(e.keyCode == 13){
            var target = e.target || e.srcElment;//srcElment针对IE
            return false;
        }
    }
}

/* 弹出层事件方法 */
var dialog = {
    msg:function(msg,cb){
        layer.msg(msg,{
            area:'300px',
            time:1200,
            success:function(){
                preventEnter();
            },
            end: cb
        });
    },
    success:function(msg,cb){
        layer.open({
            type:'0',
            title:'提 示',
            skin:'miniLayer',
            content:msg,
            btn:['确认','取消'],
            btnAlign:'c',
            success:function(){
                preventEnter();
            },
            yes:cb
        })
    },
    error:function(msg,cb){
        layer.alert(msg,{
            title:'提 示',
            skin:'miniLayer',
            icon:2,
            success:function(){
                preventEnter();
            },
            yes:cb
        })
    },
    loading:function(){
        var index = layer.load(1, {
            area:'60px',
            shade: 'transparent',
            success:function(){
                    preventEnter();
                }
            });
        return index;
    },
    confirm:function(msg,btn,succCb,errCb){
        layer.confirm(msg, {
            btn: btn, //按钮
            success:function(){
                preventEnter();
            },
            skin:'miniLayer',
            btnAlign:'c'
        }, function(){
            succCb();
        }, function(){
            errCb();
        });
    },
    alert:function(msg){
        layer.alert(msg,{
            success:function(){
                preventEnter();
            },
            skin:'miniLayer',
            btnAlign:'c',
            yes:function(index){
                layer.close(index);
            }
        })
    },
    photo:function(opt){
        layer.photos({
            photos: opt,
            closeBtn: 1,
            anim: 0 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
    },
    success_title:function(msg,cb,title){
        layer.open({
            type:'0',
            area:'400px',
            title:title,
            skin:'miniLayer',
            content:msg,
            btn:['确认','取消'],
            btnAlign:'c',
            success:function(){
                preventEnter();
            },
            yes:cb
        })
    },
};

/* 调用loading */
var load;
function showLoading(){
    load = dialog.loading();
}

function hideLoading(){
    layer.close(load);
}

/* 全选事件 */
function allchked(id,flag,all) {
    var $input = $(id),
        array = [],
        chknum = $(id).size(), //选项总个数
        chk = 0;
    $input.each(function () {

        if ($(this).prop("checked") == true) {
            chk++;
            array.push($(this).val());
        }
    });
    if(flag){
        if (chknum == chk) { //全选
            $(all).prop("checked", true);
        } else { //不全选
            $(all).prop("checked", false);
        }
    }
    return array;
}

/* 请求弹窗分页 */
function pageListAjax(url,data,object,pageFlag,modalId){
    $.post(url,data,function(e){
        layer.close(load);
        if(e.code == 0){
            var datas = e.data.data;
            var pageData = e.data;

           /*if(data.page == 1){
               var datas = {
                   totalPage:'10',
                   page:'1',
                   data:[
                       {order_id:'12222334334',status:'已确认',time:'2018年12月12日 10:00:00',company:'安好到第四啊好的',brand:'Dell',model:'的话实践活动',num:'3232',price:'133.00',total_cost:'11111'},
                       {order_id:'12222334334',status:'已确认',time:'2018年12月12日 10:00:00',company:'安好到第的',brand:'Dell',model:'的话实践活动',num:'3232',price:'133.00',total_cost:'11111'},
                       {order_id:'32434',status:'已确认',time:'2018年12月12日 10:00:00',company:'好到第好的',brand:'4334',model:'dsds',num:'3232',price:'133.00',total_cost:'11111'},
                       {order_id:'877764334',status:'已确认',time:'2018年12月12日 10:00:00',company:'安好到第四的',brand:'Dell',model:'的话实践活动',num:'3232',price:'133.00',total_cost:'11111'},
                       {order_id:'314334334',status:'已确认',time:'2018年12月12日 10:00:00',company:'安好到第四啊好的',brand:'Dell',model:'的话实践活动',num:'3232',price:'133.00',total_cost:'11111'}
                   ]
               };
           } */

            var dataAll = datas.data;
            var html = template(object.id,{data:dataAll});
            $('#'+object.wrap).html(html);

            if(modalId){
                $('#'+modalId).modal();
            }

            if(pageFlag){
                var $pageId = $('#'+object.page);
                var num = parseInt(pageData.totalpage);
                var currentPage = parseInt(pageData.curr_page);
                if(num > 1 ){
                    //这里是加载省略号的flag
                    var isHiddenExist = 0;
                    var htmlPage = '<li><a href="javascript:;" rel="pre">&laquo;</a></li>';
                    for(var i = 1;i <= num;i++){
                        if(i < 4 || i < (currentPage + 3) && i > (currentPage - 3) || i > (num - 3)){
                            htmlPage += '<li><a href="javascript:;">'+ i +'</a></li>';
                            isHiddenExist = 0;
                        }else{
                            if(isHiddenExist == 0){
                                htmlPage +=  '<li class="disabled"><span>...</span></li>';
                                isHiddenExist = 1;
                            }
                        }
                    }
                    htmlPage += '<li><a href="javascript:;" rel="next">&raquo;</a></li>';
                    $pageId.html(htmlPage).css('display','inline-block');
                }else{
                    $pageId.hide();
                }
                var $li = $pageId.children('li');
                //console.log(currentPage)
                $li.each(function(){
                    if($(this).children('a').html() == currentPage){
                        $(this).addClass('active').siblings().removeClass('active');
                    }
                });
                /*$pageId.children('li').eq(currentPage).addClass('active').siblings().removeClass('active');*/
                if(currentPage == 1){
                    $pageId.children('li').eq(0).addClass('disabled');
                }
                if(currentPage == num){
                    $pageId.children('li').last().addClass('disabled');
                }
            }

        }else {
            dialog.error(e.msg);
            return false;
        }
    },'json');
}

/* 分页点击 */
function pageClick($self){
    var $page = parseInt($self.text());
    var $currentPage = parseInt($self.parents('.page_layer').find('.active>a').text());
    /*console.log($page);
    console.log($currentPage);*/
    if($page == $currentPage || $self.parent().hasClass('disabled')){
        return false;
    }

    hideLoading();
    if($self.attr('rel') == 'next'){
        $currentPage++;
        $page = $currentPage;
    }
    if($self.attr('rel') == 'pre'){
        $currentPage--;
        $page = $currentPage;
    }
    return $page;
}


/* 单个时间选择 */
/*
* opts 选择器 | autoFlag 是否自动显示  true/false
* minDate 最小值  |  maxDate  最大值
* timeFlag 是否显示24小时  |   format 时间格式
* */
function singleTime(opts,autoFlag,minDate,maxDate,timeFlag,format){
    this.opts = opts;
    this.autoFlag = autoFlag;
    this.minDate = minDate;
    this.maxDate = maxDate;
    this.timeFlag = timeFlag;
    this.format = format;
    this.opts.daterangepicker({
        locale: {
            format: format,
            cancelLabel: '取消',
            applyLabel: "确定"
        },
        autoUpdateInput:this.autoFlag,
        singleDatePicker: true,
        showDropdowns: true,
        timePicker: this.timeFlag,
        timePicker24Hour:this.timeFlag,
        timePickerSeconds:this.timeFlag,
        opens: 'center',
        minDate : this.minDate,
        maxDate : this.maxDate,
    });
    this.opts.on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format(format));
    });
    /*this.opts.on('hide.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format(format));
    });*/
}

/* 首页公告滚动 */
/* 公告滚动 */
function AutoScroll(obj,wrap,ele){
    //console.log(obj)
    var $self = obj.find(wrap);
    console.log($self)
    //console.log(obj.height());
    //console.log($self.height());
    if($self.height() <= obj.height()){
        return false;
    }
    var lineHeight = $self.find(ele).height();
    $self.animate({
        marginTop:-lineHeight + "px"
    },600,function(){
        $self.css({marginTop:"0"}).find(ele).appendTo($self);
    });
}

/* 开始结束时间关联查询 */
var minDate = null;
var maxDate = null;
function fromDate(maxDate,$start) {
    if(!maxDate){
        max = moment(new Date())
    }else{
        max = maxDate;
    }
    $('.start-time').daterangepicker({
        autoUpdateInput: false,
        "autoApply": true, //选择日期后自动提交;只有在不显示时间的时候起作用timePicker:false
        singleDatePicker: true, //单日历
        showDropdowns: true, //年月份下拉框
        startDate:$start , //设置开始日期
        maxDate: max , //设置最大日期
        "opens": "center",
        locale: {
            format: "YYYY/MM/DD", //设置显示格式
            firstDay: 1
        }
    }, function(s) {
        toDate(s);
    });
    $('.start-time').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD'));
    });
}

function toDate(minDate,$end) {
    $('.end-time').daterangepicker({
        autoUpdateInput: false,
        "autoApply": true, //选择日期后自动提交;只有在不显示时间的时候起作用timePicker:false
        singleDatePicker: true, //单日历
        showDropdowns: true, //年月份下拉框
        startDate: $end, //设置开始日期
        maxDate: moment(new Date()), //设置最大日期
        minDate: minDate,
        "opens": "center",
        locale: {
            format: "YYYY/MM/DD", //设置显示格式
            firstDay: 1
        }
    }, function(s) {
        fromDate(s)
    });
    $('.end-time').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD'));
    });
}

$(function(){
    /*var source = '(?:' + Object.keys(escapeMap).join('|') + ')',

        Solution would be to add a polyfill for browsers that don't support Object.keys()

    Object.keys = Object.keys || function(
            o, // object
            k, // key
            r  // result array
        ) {
            // initialize object and result
            r = [];
            // iterate over object keys
            for (k in o)
                // fill result array with non-prototypical keys
                r.hasOwnProperty.call(o, k) && r.push(k);
            // return result
            return r
        };*/

    /* 美化选择器 */
    if($('select[data-name=c-select]').length > 0){
        $('select[data-name=c-select]').selectpicker();
    }

    /* 首页滚动调取 */
    /* 鼠标悬停停止滚动 */
    var $thisScroll = $("#noticeList");
    var scrollTimer;
    $thisScroll.hover(function() {
        clearInterval(scrollTimer);
    }, function() {
        scrollTimer = setInterval(function() {
            AutoScroll($thisScroll,'div','a:first');
        }, 3500);
    }).trigger("mouseleave");


    /* 电商排行滚动 */
    var rangeScrollTimer;
    var $rangeScroll = $("#rangeScroll");
    $rangeScroll.hover(function() {
        clearInterval(rangeScrollTimer);
    }, function() {
        rangeScrollTimer = setInterval(function() {
            AutoScroll($rangeScroll,'ul','li:first');
        }, 3500);
    }).trigger("mouseleave");


    /* 左侧导航高度 */
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    $('.height').css('min-height',h-70+'px');

    /* 首页导航点击子菜单 */
    $('.main_menu').on('click',function(){
        $(this).parent().toggleClass("active");
        $(this).find('.glyphicon').toggleClass("active");
        $(this).siblings('.nav-submenu').slideToggle(500);
        if($(this).siblings('.nav-submenu').find('li').hasClass('active')){
            $(this).parent().addClass('active');
        }
    });
});