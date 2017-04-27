var modelSystemHint;

var modelSystemIndex = function (){
	var button = $('#model-system-button');
	modelSystemHint = $('#model-system-hint');
	button.on('click', modelSystemLogin);
}

var modelSystemLogin = function (){
	var username = $('#model-system-username').val();
	var password = $('#model-system-password').val();
	if(!username || username == ''){
		modelSystemHint.html('用户名或密码错误');
	}
	else{
		$.ajax({
			url: modelSystemURLHeader + '/Index/validate',
			type:'POST',
			data:{'username': username, 'password': password},
			success: modelSystemCallBack,
			error: function(){modelSystemHint.html('系统错误请稍后再试');}
		});
	}
}

var modelSystemCallBack = function(data){
	data = data.trim();
	if(data.indexOf('success:') == 0){
		window.location = data.substring(8);
	}else{
		modelSystemHint.html('用户名或密码错误');
	}
}