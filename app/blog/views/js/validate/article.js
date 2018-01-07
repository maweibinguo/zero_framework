/* 添加文章时的校验规则 */
$('.submit').on('click',function(){
    var status = $(this).attr('status');
    layer.confirm(  '是否要提交该文章', 
                    {'icon':3, 'title':'提示'},
                    function(index){
                        var title = $.trim($('#title').val());
                        var tag = $.trim($('#tag').val());
                        var category = $.trim($('#category').val());
                        var headimage = $.trim($('#headimage').val());
                        var mdcontent = $.trim(testEditor.getMarkdown());
                        var htmlcontent = $.trim(testEditor.getHTML()); 
                        var article_id = $.trim($('#article_id').val())
                        var ishot = $.trim($('#ishot').val());    
                        if(title == '') {
                            layer.alert('请填写文章标题');
                            return false;
                        }
                        if(tag == '') {
                            layer.alert('请填写文章标题');
                            return false;
                        }
                        if(category == '') {
                            layer.alert('请选择文章类别');
                            return false;
                        }
                        if(headimage == '') {
                            layer.alert('请上传文章头图');
                            return false;
                        }
                        if(mdcontent == '' || htmlcontent == '') {
                            layer.alert('请编写文章内容');
                            return false;
                        }
                        if(ishot == '') {
                            layer.alert('请选择是否是今日推荐');
                        }
                        var post_data = {
                                            'title':title,
                                            'tag':tag,
                                            'mdcontent':mdcontent,
                                            'htmlcontent':htmlcontent,
                                            'status':status,
                                            'category':category,
                                            'headimage':headimage,
                                            'ishot':ishot
                                        };

                        if(article_id) {
                            post_data.article_id = article_id;
                        }
                        $.ajax({
                            'dataType':'json',
                            'url':$('.form-horizontal').attr('action'),
                            'type':'post',
                            'data':post_data,
                             'success':function(data){
                                 if(data.status == 'success') {
                                    return window.location.href='/article/view?article_id='+data.data.article_id;  
                                 } else {
                                    layer.alert(data.message, {'icon':6}); 
                                 }
                             }
                        });
                    }
                 );    
});

/**
 * ajax上传图片功能，文章头图使用
 */
$('.default_image, .upload-button').on('click',function(){
    $('#openview').click();
});

$('body').on('change', '#openview', function(){
    ajaxFileUpload();
})

function ajaxFileUpload() {
    $.ajaxFileUpload
    (
        {
            global:true,
            url: '/upload/index', //用于文件上传的服务器端请求地址
            secureuri: false, //是否需要安全协议，一般设置为false
            fileElementId: 'openview', //文件上传域的ID
            dataType: 'json', //返回值类型 一般设置为json
            complete:function(data){
                var response = $.parseJSON(data.responseText);
                if(response.success == '1') {
                    $('.default_image').attr('src', response.url);
                    $('#headimage').val(response.url);
                } else {
                    layer.alert(response.message);
                }
                $('#openview').val('');
            }
        }
    )
    return false;
}
/**
 * 编辑器上传图片功能
 */
var testEditor;
$(function() {
    testEditor = editormd("test-editormd", {
        width   : "90%",
        height  : "750px",
        syncScrolling : "single",
        path    : "/js/editormd/lib/",
        imageUpload : true,
        imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "/upload/index",
        emoji : true,
        saveHTMLToTextarea:true,
        //tex
        text:true,
        //流程图
        flowChart : true,
        //时序图
        sequenceDiagram : true,
        editorTheme : editormd.editorThemes['ambiance'],

        //全屏打开
        onfullscreen : function() {
            $('.header').hide();
            $('.tijiao').hide();
            $('.footer').hide();
        },
        //全屏关闭
        onfullscreenExit:function() {
            $('.header').show();
            $('.tijiao').show(); 
            $('.footer').show(); 
        },
        //监听变化
        onchange:function(){
                        var title = $.trim($('#title').val());
                        var tag = $.trim($('#tag').val());
                        var category = $.trim($('#category').val());
                        var headimage = $.trim($('#headimage').val());
                        var mdcontent = $.trim(testEditor.getMarkdown());
                        var htmlcontent = $.trim(testEditor.getHTML()); 
                        var article_id = $.trim($('#article_id').val());
                        var ishot = $.trim($('#ishot').val());    
                        if($.trim(mdcontent) == '') {
                            return false;
                        }
                        var post_data = {
                                            'title':title,
                                            'tag':tag,
                                            'mdcontent':mdcontent,
                                            'htmlcontent':htmlcontent,
                                            'status':status,
                                            'category':category,
                                            'headimage':headimage,
                                            'ishot':ishot,
                                            'autosave':1
                                        };

                        if(article_id) {
                            post_data.article_id = article_id;
                        }
                        $.ajax({
                            'dataType':'json',
                            'url':'/article/autoSave',
                            'type':'post',
                            'data':post_data,
                             'success':function(data){
                                 if(data.status != 'success') {
                                    layer.alert(data.message, {'icon':6}); 
                                 } else {
                                     if($('#article_id').length <= 0) {
                                        var input_str = '<input type="hidden" name="article_id" id="article_id" value="">';
                                        $('#article_form').append(input_str);
                                     }
                                    $('#article_id').val($.trim(data.data.article_id));
                                 }
                             }
                        });
        }
        //监听结束
    });
});
