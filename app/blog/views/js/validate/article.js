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
                        $.ajax({
                            //'type':'json',
                            'url':'/article/addarticle',
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
})
