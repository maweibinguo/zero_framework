/* 添加轮播图的校验规则 */
$('.submit').on('click',function(){
    var url = $(this).attr('url');
    layer.confirm(  '是否要添加轮播图', 
                    {'icon':3, 'title':'提示'},
                    function(index){
                        var picturename  = $.trim($('#picturename').val());
                        var targeturl = $.trim($('#targeturl').val());
                        var status = $('input[name="status"]').val();
                        var headimage = $.trim($('#headimage').val());
                        var picture_id = $('#picture_id').val();
                        if(picturename== '') {
                            layer.alert('请填写轮播图名称');
                            return false;
                        }
                        if(targeturl == '') {
                            layer.alert('请填写轮播图跳转地址');
                            return false;
                        }
                        if(status == '') {
                            layer.alert('请选择轮播图状态');
                            return false;
                        }
                        if(headimage == '') {
                            layer.alert('请上传banner图');
                            return false;
                        }
                        var post_data = {
                                            'picturename':picturename,
                                            'targeturl':targeturl,
                                            'status':status,
                                            'headimage':headimage
                                        };

                        if(picture_id) {
                            post_data.picture_id= picture_id;
                        }
                        $.ajax({
                            'dataType':'json',
                            'url': url,
                            'type':'post',
                            'data':post_data,
                             'success':function(data){
                                 if(data.status == 'success') {
                                    return window.location.href='/picture/index';  
                                 } else {
                                    layer.alert(data.message, {'icon':6}); 
                                 }
                             }
                        });
                    }
                 );    
});
