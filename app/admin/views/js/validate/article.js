$('#show').on('click',function(){
   var article_id = $(this).attr('article_id');
   layer.confirm(  '是否要展示该文章', 
                    {'icon':3, 'title':'提示'},
                    function(index) {
                        if(article_id == '') {
                            layer.alert('文章编号不正确'); 
                        }
                        $.ajax({
                            'dataType':'json',
                            'url':'/article/show',
                            'type':'post',
                            'data':{'article_id':article_id},
                            'success':function(data){
                                 if(data.status == 'success') {
                                    layer.alert(data.message);
                                    window.location.reload();
                                 } else {
                                    layer.alert(data.message, {'icon':6}); 
                                 }
                             }
                        });
                    }
                 );
});

$('#hidden').on('click',function(){
   var article_id = $(this).attr('article_id');
   layer.confirm(  '是否要隐藏该文章', 
                    {'icon':3, 'title':'提示'},
                    function(index) {
                        if(article_id == '') {
                            layer.alert('文章编号不正确'); 
                        }
                        $.ajax({
                            'dataType':'json',
                            'url':'/article/hidden',
                            'type':'post',
                            'data':{'article_id':article_id},
                            'success':function(data){
                                 if(data.status == 'success') {
                                    layer.alert(data.message);
                                    window.location.reload();
                                 } else {
                                    layer.alert(data.message, {'icon':6}); 
                                 }
                             }
                        });
                    }
                 );
});
