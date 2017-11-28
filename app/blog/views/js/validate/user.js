$(document).ready(function(){
    $('#login_form').submit(function(){
        var user_name = $.trim($('#user_name').val());
        var password = $('#password').val();
        var captcha = $('#captcha').val();
        if(user_name == '') {
            layer.alert('用户名必填');
            return false;
        }
        if(password == '') {
            layer.alet('登录密码必填');
            return false;
        }
        if(captcha == '') {
            layer.alert('验证码不正确');
            return false;
        }
        $.ajax({
            'dataType':'json',
            'url':'/acc/login',
            'type':'post',
            'data':{
                'user_name' : user_name,
                'password' : password,
                'captcha'  : captcha
            },
            'success':function(data){
                if(data.status == 'success') {
                    window.location.href="/index/index"
                } else {
                    layer.alert(data.message); 
                    if(data.message == '验证码不正确') {
                        $('.img_captcha').click(); 
                    }
                }
            }
        })
        return false;
    })  
});
