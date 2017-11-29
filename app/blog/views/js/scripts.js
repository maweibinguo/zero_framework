//当浏览器窗口大小改变时重载网页
/*window.onresize=function(){
    window.location.reload();
}*/
 
//页面加载
$('body').show();
$('.version').text(NProgress.version);
NProgress.start();
setTimeout(function () {
    NProgress.done();
    $('.fade').removeClass('out');
}, 1000);

//页面加载时给img和a标签添加draggable属性
(function () {
    $('img').attr('draggable', 'false');
    $('a').attr('draggable', 'false');
})();
 
//设置Cookie
function setCookie(name, value, time) {
    var strsec = getsec(time);
    var exp = new Date();
    exp.setTime(exp.getTime() + strsec * 1);
    document.cookie = name + "=" + escape(value) + ";expires=" + exp.toGMTString();
}
function getsec(str) {
    var str1 = str.substring(1, str.length) * 1;
    var str2 = str.substring(0, 1);
    if (str2 == "s") {
        return str1 * 1000;
    } else if (str2 == "h") {
        return str1 * 60 * 60 * 1000;
    } else if (str2 == "d") {
        return str1 * 24 * 60 * 60 * 1000;
    }
}
 
//获取Cookie
function getCookie(name) {
    var arr, reg = new RegExp("(^| )" + name + "=([^;]*)(;|$)");
    if (arr = document.cookie.match(reg)) {
        return unescape(arr[2]);
    } else {
        return null;
    }
}
 
//导航智能定位
/* $.fn.navSmartFloat = function () {
    var position = function (element) {
        var top = element.position().top,
            pos = element.css("position");
        $(window).scroll(function () {
            var scrolls = $(this).scrollTop();
            if (scrolls > top) { //如果滚动到页面超出了当前元素element的相对页面顶部的高度
                $('.header-topbar').fadeOut(0);
                if (window.XMLHttpRequest) { //如果不是ie6
                    element.css({
                        position: "fixed",
                        top: 0
                    }).addClass("shadow");
                } else { //如果是ie6
                    element.css({
                        top: scrolls
                    });
                }
            } else {
                $('.header-topbar').fadeIn(500);
                element.css({
                    position: pos,
                    top: top
                }).removeClass("shadow");
            }
        });
    };
    return $(this).each(function () {
        position($(this));
    });
};*/
 
//启用导航定位
/* $("#navbar").navSmartFloat();*/
 
//返回顶部按钮
$("#gotop").hide();
$(window).scroll(function () {
    if ($(window).scrollTop() > 100) {
        $("#gotop").fadeIn();
    } else {
        $("#gotop").fadeOut();
    }
});
$("#gotop").click(function () {
    $('html,body').animate({
        'scrollTop': 0
    }, 500);
});
 
//图片延时加载
$("img.thumb").lazyload({
    placeholder: "/Home/images/occupying.png",
    effect: "fadeIn"
});
$(".single .content img").lazyload({
    placeholder: "/Home/images/occupying.png",
    effect: "fadeIn"
});
 
//IE6-9禁止用户选中文本
/*document.body.onselectstart = document.body.ondrag = function () {
    return false;
};*/
 
//启用工具提示
$('[data-toggle="tooltip"]').tooltip();
 
//鼠标滚动超出侧边栏高度绝对定位
$(window).scroll(function () {
    var sidebar = $('.sidebar');
    var sidebarHeight = sidebar.height();
    var windowScrollTop = $(window).scrollTop();
    if (windowScrollTop > sidebarHeight - 60 && sidebar.length) {
        $('.fixed').css({
            'position': 'fixed',
            'top': '70px',
            'width': '360px'
        });
    } else {
        $('.fixed').removeAttr("style");
    }
});

/*文章评论*/
$(function(){
	$("#comment-submit").click(function(){
		var commentContent = $("#comment-textarea");
		var commentButton = $("#comment-submit");
		var promptBox = $('.comment-prompt');
		var promptText = $('.comment-prompt-text');
		var articleid = $('.articleid').val();
        var captcha = $.trim($('.captcha_comment').val());
		promptBox.fadeIn(400);
		if(commentContent.val() === ''){
			promptText.text('请留下您的评论');
			return false;
		} 

        if(captcha == '') {
            layer.alert('请填写验证码');
            return false;
        }

		commentButton.attr('disabled',true);
		commentButton.addClass('disabled');
		promptText.text('正在提交...');

		$.ajax({   
			type:"POST",
			url:"/article/addcomment",
            dataType:'json',
			data:{"article_comment":replace_em(commentContent.val()), 'article_id':articleid, 'captcha':captcha},
			cache:false, //不缓存此页面  
			success:function(response){
                var data = response.data;
			    commentContent.val(null);
				$(".commentlist").fadeIn(300);
				commentButton.attr('disabled',false);
				commentButton.removeClass('disabled');
            
                if(response.status != 'success') {
                    layer.alert(response.message);
                    $('.img_captcha').click();
                    return false;
                }
                //添加评论
                /*var li_str = '<li class="comment-content"><span class="comment-f">'+data.id_num+'</span>\
                                <div class="comment-avatar"><img class="avatar" src="/images/icon/icon.png" alt="" /></div>\
                                <div class="comment-main">\
                                  <p>来自用户<span class="address">zero</span>(<span class="time">'+data.add_time+'</span>)<br />\
                                    <span class="content">'+ data.article_comment +'</span>\
                                  </p>\
                                </div>\
                              </li>';
                $('.commentlist').append(li_str);*/
				promptText.text('评论成功!');
			}
		});
		promptBox.fadeOut(100);
		return false;
	});
});

//对文章内容进行替换
function replace_em(str){
	str = str.replace(/\</g,'&lt;');
	str = str.replace(/\>/g,'&gt;');
	str = str.replace(/\[em_([0-9]*)\]/g,'<img src="/Home/images/arclist/$1.gif" border="0" />');
	return str;
}

//Console
try {
    if (window.console && window.console.log) {
        console.log("\n欢迎访问insisting博客！\n\n在本站可以看到前端技术，后端程序，网站内容管理系统等文章；\n\n还有我的程序人生！！！\n");
        console.log("\n请记住我们的网址：www.insisting.top", "color:red");
        console.log("\nPOWERED BY WY ALL RIGHTS RESERVED");
    }
} catch (e) {};


//验证码
$('.img_captcha').click(function(){
    search = window.location.search;
    var url = $(this).attr('src').replace(/\?.*/, '');
    url = url + '?' +Math.random();
    $(this).attr('src', url);
})
