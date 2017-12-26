$(function() {
        var testEditormdView;
        var url = "/article/getMdContent" + window.location.search;
        $.ajax({
            'url':url,
            'dataType':'json',
            'success': function(api_data){
                testEditormdView = editormd.markdownToHTML("article_content", {
                    markdown        : api_data.data.mdcontent,
                    htmlDecode      : "style,script,iframe", 
                    tocm            : true, 
                    emoji           : true,
                    taskList        : true,
                    tex             : true,  // 默认不解析
                    flowChart       : true,  // 默认不解析
                    sequenceDiagram : false,  // 默认不解析
                    codeFold : true, //ctrl+q代码折叠
                    theme : 'dark',
                    previewTheme : 'dark'
                });
                
            }
        });

        $('#delete_article').on('click', function(){
            var article_id = $(this).attr('article_id');
            layer.confirm(  '是否要删除该文章', 
                            {'icon':3, 'title':'提示'},
                            function(index) {
                                    $.ajax({
                                        'dataType':'json',
                                        'data':{'article_id':article_id},
                                        'type':'post',
                                        'url':"/article/delete",
                                        'success':function(data){
                                             if(data.status == 'success') {
                                                return window.location.href='/index/index';  
                                             } else {
                                                layer.alert(data.message, {'icon':6}); 
                                             }
                                         }
                                    });
                            }
                         );
        });
});
