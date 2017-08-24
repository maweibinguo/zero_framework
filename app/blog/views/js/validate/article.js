/* 添加文章时的校验规则 */
$('.submit').on('click',function(){
    var status = $(this).attr('status');
    layer.confirm(  '是否要提交该文章', 
                    {'icon':3, 'title':'提示'},
                    function(index){
                        var title = $.trim($('#title').val());
                        var tag = $.trim($('#tag').val());
                        var mdcontent = $.trim(testEditor.getMarkdown());
                        var htmlcontent = $.trim(testEditor.getHTML()); 
                        if(title == '') {
                            layer.alert('请填写文章标题');
                            return false;
                        }
                        if(tag == '') {
                            layer.alert('请填写文章标题');
                            return false;
                        }
                        if(mdcontent == '' || htmlcontent == '') {
                            layer.alert('请编写文章内容');
                            return false;
                        }
                        $.ajax({
                            'type':'json',
                            'url':$('.form-horizontal').attr('action'),
                            'type':'post',
                            'data':{
                                        'title':title,
                                        'tag':tag,
                                        'mdcontent':mdcontent,
                                        'htmlcontent':htmlcontent,
                                        'status':status
                                   },
                             'success':function(data){
                                 if(data.status == 'success') {
                                    var article_key_name = data.data.article_key_name; 
                                    return window.location.href='/article/getArticle?article_id='+article_key_name;  
                                 } else {
                                    layer.alert(data.message, {'icon':6}); 
                                 }
                             }
                        });
                    }
                 );    
});

var testEditor;

$(function() {
    testEditor = editormd("test-editormd", {
        width   : "100%",
        height  : "750px",
        syncScrolling : "single",
        path    : "/js/editormd/lib/",
        imageUpload : true,
        imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
        imageUploadURL : "/upload/index",
        saveHTMLToTextarea:true,
        //tex
        text:true,
        //流程图
        flowChart : true,
        //时序图
        sequenceDiagram : true,
        editorTheme : editormd.editorThemes['ambiance'],
        
    });
});
