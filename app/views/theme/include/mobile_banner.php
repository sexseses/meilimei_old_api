<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1,user-scalable=false">
<script src="http://www.meilimei.com/zhuanti/css/jquery.js" type="text/javascript"></script>
<script src="http://www.meilimei.com/zhuanti/css/jquery.lazyload.js" type="text/javascript"></script>
<?php if($infos[0]['id']=='87'):?>
<link rel="stylesheet" type="text/css" href="http://www.meilimei.com/zhuanti/css/zhuanti87.css" >
<script src="http://www.meilimei.com/zhuanti/js/choujiang2.js" type="text/javascript"></script>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3Ffab9a541f52525760d44dfdc1b223871' type='text/javascript'%3E%3C/script%3E"));
</script>
<?php endif; ?>
<script type="text/javascript">
$("img.lazy").lazyload();
</script>
<title><?php
if(!empty($infos)){ 
  echo $infos[0]['title'];
}
?></title>
</head>
<body>
<?php if(isset($infos[0]['tehuiid'])):?>
<script language="javascript">
	function loadURL(url) {
		var iFrame;
		iFrame = document.createElement("iframe");
		iFrame.setAttribute("src", url);
		iFrame.setAttribute("style", "display:none;");
		iFrame.setAttribute("height", "0px");
		iFrame.setAttribute("width", "0px");
		iFrame.setAttribute("frameborder", "0");
		document.body.appendChild(iFrame);
		iFrame.parentNode.removeChild(iFrame);
		iFrame = null;
	}
</script>
<span id="nxsbutco" style="display:none;"><?php echo $infos[0]['tehuiid']; ?></span>
<?php endif; ?>

<style type="text/css">
        *{margin:0px;padding:0px;border:0px; font-family: Microsoft YaHei;}
        .mainc{font-size:16px;line-height:180%;max-width:600px;/*padding:10px;*/color:#333;margin:auto;}
		.mainc .info {padding:0px 10px;}
         .mainc img{max-width:640px;}
         .mainc img {vertical-align: top; width: 100%;}
        .ie6 img { width:100%; }
input[type="checkbox"], input[type="radio"] { box-sizing: border-box; padding: 0; *width: 13px; *height: 13px; }
input[type="search"] { -webkit-appearance: textfield; -moz-box-sizing: content-box; -webkit-box-sizing: content-box; box-sizing: content-box; }
input[type="search"]::-webkit-search-decoration, input[type="search"]::-webkit-search-cancel-button { -webkit-appearance: none; }

.wapper_form{ width:95%; margin:0 auto 20px;  }
.forms{}
.forms ul{}
.forms ul li{ list-style:none; margin:1% 0; color:#ff6899; padding:0 10px;}
.forms ul li input{ width:100%; height:40px; line-height:40px; border:solid 1px #dadada; }
.forms button{ background:#ff6899; color:#fff;margin-top:20px; text-align:center; font-size:120%;border-radius:1px; width:90%;line-height:40px;  border:solid 1px #ff6899;  font-family: Microsoft YaHei;border-radius: 5px;}
.zhuanti ul ,.zhuanti li {list-style:none; margin:0; padding:0;}
.zhuanti img {max-width: inherit !important;}
.zhuanti .bnxbut {margin: 5px 0 15px !important; text-align: center;}
.zhuanti .bnxbut a {background:#ffa200; border-radius: 5px; color: #fff; cursor: pointer; display: inline-block;  font-size: 24px; letter-spacing: 5px; padding: 10px 0; text-decoration: none; width: 96%;text-align: center;}
.butdiv {background-color: #000;  height: 100%; left: 0; top: 0; width: 100%;  position: fixed;  -moz-opacity: 0.7; opacity:.70; filter: alpha(opacity=70); z-index: 9;}

#showsuccess {position: relative;}
.sub86 {-webkit-box-shadow:1px 1px 2px 0 rgba(0,0,0,0.22);-moz-box-shadow:1px 1px 2px 0 rgba(0,0,0,0.22);box-shadow:1px 1px 2px 0 rgba(0,0,0,0.22);border-radius:5px;background:#fff;  left: 10%; padding: 20px 30px; position: absolute; text-align: center; width: 70%; z-index: 99;}
.sub86 h3 {color: #5a5a5a; font-size: 32px; font-weight: normal; line-height: inherit;}
.sub86 .sub86ok { color: #5a5a5a; display: block; font-size: 18px; letter-spacing: 1px; margin-top: 10px;}
.sub86 .sub86info {  color: #ff6894; display: block; line-height: 24px; margin-top: 15px;}
.sub86 .sub86shop { background: #ff6894; border-radius: 5px; color: #fff; display: block; font-size: 20px; letter-spacing: 5px; margin-top: 10px; padding: 10px 0; text-decoration: none; width: 100%;}
.sub86 .sub86app {color: #666; display: block; letter-spacing: 2px; margin-top: 10px;}
@media only screen and (max-width: 479px){
	.sub86 {width: 65%;}
	.downapp  { left: 0%;}
}
</style>

<?php if($infos[0]['id']=='87'):?>
	<div id="showsuccess" class="mainc" style="<?php echo  $this->input->get('state')?'':'display:none' ?>">
	<div class="butdiv"></div>
	</div>
<?php else:?>
	<div id="showsuccess" class="mainc" style="<?php echo  $this->input->get('state')?'':'display:none' ?>">
	<div class="butdiv"></div>
	<div class="sub86">
	<?php echo $infos[0]['success_content'] ?>
	</div>
	</div>
<?php endif; ?>

  <div id='content' class="mainc">
  <div class="info" style="<?php echo ($infos[0]['success_content']!='' and $this->input->get('state'))?'display:none':'' ?>">
<?php
if(!empty($infos)){ 
  echo $infos[0]['content'];
}
if(!empty($survey)){
?> </div>
<div class="wapper_form"> 
        <div class="forms" style="text-align: left;">
        <h3 id="jump"><?php echo $infos[0]['sur_title'] ?></h3>
		
		<?php echo form_open('banner/survey/'.$infos[0]['id'],array('id' => 'myform','enctype' => 'multipart/form-data')); ?>
		
		<?php $i=0; foreach(array_reverse($survey) as $r): //array_reverse($survey)?>
			<?php if($r['title']=='图片'):?>
			<script src="http://www.meilimei.com.cn/public/js/ajaxfileupload.js" type="text/javascript"></script>
				<script type="text/javascript">
				$(document).ready(function(){
					$(".setadd input").change( function() {
						var thisid = $(this).attr('id');
						//var aaa = $(this);
						$(this).after('<img style="display:none" class="loading" src="http://www.meilimei.com/images/loading.gif">');
						$('.loading').ajaxStart(function(){ $(this).show();  }).ajaxComplete(function(){ $(this).remove(); });
						$.ajaxFileUpload
							({
									url:'http://www.meilimei.com/banner/bannerimg',
									secureuri:true,
									fileElementId:thisid,
									dataType: 'json',
									data:{name:thisid},
									success: function (data, status)
									{
										if(typeof(data.error) != 'undefined')
										{
											if(data.error != '')
											{
												alert(data.error);
											}else
											{
												$('#'+thisid).prev().children('img').attr('src','http://pic.meilimei.com.cn/upload/'+data.msg);
												var imgds= $('.imgdisplay').val();
												var dataimg = '{{'+data.msg+'}}';
												if(imgds!=''){
													$('.imgdisplay').val(imgds+dataimg);
												}else{
													$('.imgdisplay').val(dataimg);
												}
												//alert(data.msg);
											}
										}
									},
									error: function (data, status, e)
									{
										alert(e);
									}
							})
						
						return false;
					});
				});
					</script>
				<ul class="upimg zxbw">
					<li class="zxbwlin"><span>你想整形的部位</span></li>
					<li class="on1"><span>上传您的素颜照（正面，侧面）</span></li>
					<li>
						<input name="survey_name[<?php echo $i; ?>]" value="<?php echo $r['title']; ?>" type="hidden">
						<input class="imgdisplay" name="survey[<?php echo $i; ?>]" type="text" autocomplete="off">
						<div class="setaddall">
							<div class="setadd"><div class="phupload"><img src="http://www.meilimei.com/images/upico.jpg"></div><input type="file" name="badd1" id="badd1" /></div>
							<div class="setadd"><div class="phupload"><img src="http://www.meilimei.com/images/upico.jpg"></div><input type="file" name="badd2" id="badd2" /></div>
							<div class="setadd"><div class="phupload"><img src="http://www.meilimei.com/images/upico.jpg"></div><input type="file" name="badd3" id="badd3" /></div>
						</div>
						<div class="bannerimgcot"><i></i>上传照片数量增加可使<span>中奖几率</span>变高</div>
					</li>
				</ul>
			<?php elseif($r['title']=='整形部位'):?>
				<script type="text/javascript">
					$(document).ready(function(){
						$('.zxbwlist span').click(function(){
							var remov=$(this).attr('classto');
							if($(this).hasClass("selected")){
								$(this).removeClass('selected');
								$("#"+remov).remove();
								$("#textarea").val($("#textarea").html());
							}else{
								$(this).addClass("selected");
								$("#textarea").append("<span id="+remov+">"+$(this).html()+"</span>");
								$("#textarea").val($("#textarea").html());
							}
						});
					});
				</script>
				<ul class="zxbw" style="padding-bottom: 0px; overflow: hidden;">
					
					<li class="zxbwcot">
					<input name="survey_name[<?php echo $i; ?>]" value="<?php echo $r['title']; ?>" type="hidden">
					<input placeholder="输入你想整形的部位..." autocomplete="off" required="required"autocomplete="off" required="required"  id="textarea"  name="survey[<?php echo $i; ?>]" type="text" style="display:block;">
					</li>
				</ul>
			<?php elseif($r['title']=='整形内容'):?>
				<ul class="zxbw" style="padding-top: 0px; overflow: hidden;">
					<li><input name="survey_name[<?php echo $i; ?>]" value="<?php echo $r['title']; ?>" type="hidden">
					<textarea placeholder="写出您整容背后的故事..." autocomplete="off" required="required" name="survey[<?php echo $i; ?>]" cols="45" rows="5"></textarea>
					</li>
				</ul>
			<?php elseif($r['title']=='注射部位'):?>
				<script type="text/javascript">
					$(document).ready(function(){
						$('.zxbwlist span').click(function(){
							var remov=$(this).attr('classto');
							if($(this).hasClass("selected")){
								$(this).removeClass('selected');
								$("#"+remov).remove();
								$("#textarea").val($("#textarea").html());
							}else{
								$(this).addClass("selected");
								$("#textarea").append("<span id="+remov+">"+$(this).html()+"</span>");
								$("#textarea").val($("#textarea").html());
							}
						});
					});
				</script>
				<ul class="zxbw" style="padding-bottom: 0px; overflow: hidden;">
					<li class="zxbwlin"><span>你想注射的部位</span></li>
					<li class="zxbwcot">
					<div class="zxbwlist"><span classto="list1">脸部</span><span classto="list2">肢体</span><span classto="list3">其他</span></div>
					<input name="survey_name[<?php echo $i; ?>]" value="<?php echo $r['title']; ?>" type="hidden">
					<input autocomplete="off" required="required"  id="textarea"  name="survey[<?php echo $i; ?>]" type="text">
					</li>
				</ul>
			<?php else:?>
				<ul>
					<li class="on1"><?php echo $r['title']; ?></li>
					<li><input name="survey_name[<?php echo $i; ?>]" value="<?php echo $r['title']; ?>" type="hidden"><input autocomplete="off" required="required" name="survey[<?php echo $i; ?>]" type="text"></li>
				</ul>
			<?php endif; ?>
		<?php $i++; endforeach;?>

			<input type="hidden" name="emails" value="<?php echo $infos[0]['email'];?>">
			<input type="hidden" name="banner_id" value="<?php echo $infos[0]['id'] ?>">
            <ul style="text-align: center;">
                <li><button id="sendinfo" type="button"  class="on2">提交信息</button></li>
            </ul>
	</form>
        </div>
    </div>
<?php } ?>

<script type="text/javascript">
$(function(){
	$("#sendinfo").click(function(){
		if($("#showsuccess").text().length<2){
			$(".wapper_form").hide();
		}else{
			$("#content .info").hide();
		}
		$("#myform").submit();
		$("#showsuccess").show()
		
		$('html').animate(
			{ scrollTop: '0px' }, 1000
		);
		
	}); 
 })
 </script>
<span id="footerco"></span>
 <?php if($infos[0]['id']=='87'):?>
<div class="taobaoatt" style="display:none;">
<div class="taobaoattco">
	<p>请提交完整个人信息，便于我们派送奖品。</p>
	<input type="text" value="淘宝助手抽奖活动"  hidden="" id="event_name" name="event_name">
	<input type="text" value="" name="event_mobile" id="event_mobile" autocomplete="off" placeholder="手机号" required="required" maxLength="11" onkeyup="value=this.value.replace(/\D+/g,'')">
	<input type="text" value="" name="user_name" id="user_name" autocomplete="off" placeholder="姓名" required="required" >
	<input type="text" value="" name="event_content" id="event_content" autocomplete="off" placeholder="详细收货地址" required="required" >
	<span id="woo">试试手气~</span>
</div>
</div>
 <?php endif; ?>
</div>
</body>
</html>