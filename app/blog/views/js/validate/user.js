$(document).ready(function(){
    $('#login_form').submit(function(){
        var user_name = $.trim($('#user_name').val());
        var password = $('#password').val();
        if(user_name == '') {
            layer.alert('用户名必填');
            return false;
        }
        if(password == '') {
            layer.alet('登录密码必填');
            return false;
        }
        $.ajax({
            'dataType':'json',
            'url':'/acc/login',
            'data':{
                'user_name' : user_name,
                'password' : password
            },
            'success':function(data){
                if(data.status == 'success') {
                    window.location.href="/index/index"
                } else {
                    layer.alert(data.message); 
                }
            }
        })
        return false;
    })  
});
