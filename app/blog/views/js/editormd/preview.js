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
});
