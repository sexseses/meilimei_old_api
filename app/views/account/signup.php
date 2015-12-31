<?php echo validation_errors(); ?>
<?php// echo form_open('/user/signup'); ?>
<div class="butdiv87"></div>
<div class="userall">
<div class="userallco">
<div class="fanico"><img src="http://www.meilimei.com/images/zhuanti/87/fanico.png" /></div>
	<div class="register">
		<div class="usertit"><span>注册</span></div>
		<div class="logings">登录></div>
		<input id="phone" type="text" name="phone" value=""  autocomplete="off" placeholder="手机号"  maxLength="11" onkeyup="value=this.value.replace(/\D+/g,'')" required="required" autofocus/>
		<div class="regp">
			<input id="captcha" type="text" name="captcha" value=""  autocomplete="off" placeholder="验证码"  maxLength="6" onkeyup="value=this.value.replace(/\D+/g,'')" required="required"/>
			<input type="button" value="获取验证码" class="capsend" id="capsend" name="capsend">
		</div>
		<input id="password" type="text" name="password" value="" autocomplete="off" placeholder="密码" required="required"  maxLength="30">
		<button class="button" id="subreg">~~我要注册~~</button>
		<span class="captcha">收不到验证码？</span>
	</div>

	<div class="login">
		<div class="usertit"><span>登录</span></div>
		<div class="regpas">忘记密码></div>
		
			<input type="text" value="" name="username" id="username" autocomplete="off" placeholder="手机号" required="required" maxLength="11" onkeyup="value=this.value.replace(/\D+/g,'')">
			<input type="password" value="" name="logpassword" id="logpassword" autocomplete="off" placeholder="密码" required="required">
			<button class="button" id="loginup">登录</button>

			<div class="logreg">~~我要注册~~</div>

	</div>

	<div class="findpass">
		<div class="usertit"><span>忘记密码</span></div>
		<div class="logings">登录></div>
		<input id="newphone" type="text" name="newphone" value=""  autocomplete="off" placeholder="找回密码的手机号"  maxLength="11" onkeyup="value=this.value.replace(/\D+/g,'')" required="required" autofocus/>
		<div class="regp">
			<input id="newcaptcha" type="text" name="newcaptcha" value=""  autocomplete="off" placeholder="验证码"  maxLength="6" onkeyup="value=this.value.replace(/\D+/g,'')" required="required"/>
			<input type="button" value="获取验证码" class="newcapsend" id="newcapsend" name="newcapsend">
		</div>
		<input id="newpassword" type="text" name="newpassword" value="" autocomplete="off" placeholder="设置新密码" required="required"  maxLength="30">
		<button class="button" id="findpassup">~~确认修改密码~~</button>
		</div>
</div>
</div>