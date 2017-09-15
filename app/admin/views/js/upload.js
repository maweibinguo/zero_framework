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
