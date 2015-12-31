            <div class="page_content926">
            	<div class="register_main"><?php if($notlogin){ ?>
                    <div class="register_left">
                    	<div class="re_tab">
                        	<ul>
                                <li name="3" <?php echo $utype==1?'class="on"':''?> id="geren"><a href="#" class="selected3">我是个人</a></li>
                                <li name="2" <?php echo $utype==2?'class="on"':''?> id="yishi"><a href="#" class="selected2">我是医师</a></li>
                                <li name="1" <?php echo $utype==3?'class="on"':''?> id="jigou"><a href="#" class="selected1">我是医院/机构</a></li>
                            </ul>
                        </div>
                        <div class="re_box"> <form id="reg" accept-charset="utf-8" method="post" action="<?php echo site_url('user/reg'); ?>">       
                        	<ul id="regisbox">
                            	<li><h5>我想成为美丽诊所的一员，快速注册</h5></li><input type="hidden" name="utype" id="utype" value="<?php echo $utype ?>">
                                <li><input name="uname" onfocus="if(this.value=='请输入你的邮箱或手机号') this.value=''" onblur="if(this.value=='') this.value='请输入你的邮箱或手机号'; " type="text" class="username required" value="请输入你的邮箱或手机号"> <?php echo form_error('uname'); ?></li>
                                <li><input name="upass" onfocus="javascript:gethash()" type="password" id="upass" class="password required" value="<?php if($this->input->post('upass')){echo $this->input->post('upass'); }else{echo ''; } ?>"> <?php echo form_error('upass'); ?></li>
                                <li class="on"><input name="hasread"  type="checkbox" class="choose_input required" id="hasread" checked="checked">已阅读并同意<a href="#">服务条款</a></li> <li id="submitpos"><input name="regbuton" id="regbuton" type="submit" value="" disabled="disabled" class="off_button"></li>
                            </ul></form> 
                        </div>
                    </div><script type="text/javascript"> 
					$(function (){
					  $(".re_tab ul li").click(function(){$(".re_tab ul li").removeClass(); $("form").hide(); $(this).addClass("on");if($(this).attr("id")=="yishi"){$("#utype").val(2);}else if($(this).attr("id")=="geren"){$("#utype").val(1);}else{$("#utype").val(3) }$("form").show(300); });  });
					function gethash(){ 
					  var _obj = $("#regisbox");
					  if($('#regbuton').hasClass("off_button")){$('#regbuton').removeClass("off_button").addClass("makesure_button");
						  $.get('<?php echo site_url('state/gethash'); ?>', {}, function(data) { 
                          _obj.append(data);$('#regbuton').removeAttr("disabled"); 
	                  }) 
					  } 
					}
</script><?php }else{if($this->wen_auth->get_role_id()==16){
                           redirect('manage');
						}elseif($this->wen_auth->get_role_id() == 1 || $this->wen_auth->complete=!false){
							redirect('user/dashboard');
						}elseif($this->wen_auth->complete==false){ if($this->wen_auth->nextStep==2){
                             redirect('user/yishengReg');
                         }else{
                         redirect('user/jigouReg');
                         }  }  } ?>
                    <div class="register_right">
                        <h1>已有美丽诊所账号, 立即登录</h1>
                        <input onclick="location.href='<?php echo base_url() ?>user/login'" type="button" class="login1">
                    </div>
                </div>
            </div>
		</div>
        <div class="notice_info">
        	<ul>
            	<li>
                	<img src="http://static.meilimei.com.cn/public/images/icon1.png" width="95" height="95">
                	<h2>专业机构</h2>
                    <h5>使用美丽诊所直接<br><span>检索</span><br>你身边的美容医师</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images/icon2.png" width="95" height="95">
                	<h2>了解更多的美容<br>健康信息</h2>
                    <h5><span>美丽</span>诊所涵盖全面的<span>资讯</span><br>让你在指尖私密地阅览<br>介绍和价格等</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images/icon3.png" width="95" height="95">
                	<h2>分不清哪家美容医院<br>或医师更好</h2>
                    <h5>得益于一套<span>公平中立的评价体系</span><br>我们让你看到美容行业<br>真实的那一面</h5>
                </li>
                <li class="on">
                	<img src="http://static.meilimei.com.cn/public/images/icon4.png" width="95" height="95">
                	<h2>想找人聊聊变美的<br>心得和效果</h2>
                    <h5>美丽诊所专注于爱美之人的圈子<br>让大家私密而无所忌惮地<br><span>讨论美丽</span></h5>
                </li>
            </ul>
        </div> 
 