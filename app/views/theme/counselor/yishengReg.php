<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.insert.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('counselor/myyishi/') ?>">医师管理</a> </li><li><a href="<?php echo site_url('counselor/similaryishi/') ?>">可能所属医师</a></li><li class="on"><a href="<?php echo site_url('counselor/addyishi/') ?>">添加医师</a></li> 
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                         <div class="institutions_info"><?php echo form_open_multipart("",array('id' => 'reg'))?><script type="text/javascript">
$(function(){
	$("#otherpics").easyinsert();  
});
</script>
                	<div class="institutions_info_left" style="padding:0px 0px 0px 30px">
                    	<h5>医师信息录入</h5>
                    	<ul>
                        	<li class="f1">*</li>
                            <li class="f2">姓</li>
                            <li class="f3"><input name="Lname" id="Fnamerequired" value="<?php echo set_value('Fname'); ?>" type="text"><?php echo form_error('Fname'); ?></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                   	    <li class="f1">*</li>
                            <li class="f2">名</li>
                            <li class="f3"><input name="Fname" id="Lnamerequired" value="<?php echo set_value('Lname'); ?>" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">性别</li>
                            <li class="f8"><input name="sex" type="radio" value="1" checked="checked">女<input name="sex" type="radio" value="2">男</li>
                            <li class="f4"> </li>
                        </ul><ul>
                        	<li class="f1">*</li>
                            <li class="f2">密码</li>
                            <li class="f8"><input name="upass" value="" id="upass" type="text"></li>
                            <li class="f4"> </li>
                        </ul>
                        <ul>
                        	<li class="f1"></li>
                            <li class="f2">职称</li>
                            <li class="f3"><input name="position" type="text"></li>
                            <li class="f4">（博士、主任、教授等）</li>
                        </ul>
                         <ul>
                        	<li class="f1"> </li>
                            <li class="f2">手机号码</li>
                            <li class="f3"><input name="phone" value="" type="text"></li>
                            <li class="f4">（可回答用户咨询）</li>
                        </ul><ul>
                        	<li class="f1"> </li>
                            <li class="f2">咨询手机</li>
                            <li class="f3"><input name="assistphone" value=" " type="text"></li>
                            <li class="f4">（如有专人负责医师咨询，请输入TA的手机号）</li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">Email</li> 
                            <li class="f3"><input name="email" id="emailcheck" value="" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">个人简介</li>
                            <li class="f6"><textarea name="introduce"  id="introduce"></textarea></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">擅长领域</li>
                            <li class="f6"><textarea name="skilled" id="skilled" ></textarea></li>
                            <li class="f4-1">（请详细描述）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f7">所在科室<br>(可多选)</li>
                            <li class="f8"><?php foreach($keshi as $k=>$val){
								echo '<input name="department['.$k.']" type="checkbox"  value="'.$k.'" class="selected">'.$val;
								} ?> </li>
                            <li class="f4"></li>
                        </ul>
                       
                        <ul>
                            <li class="f1"></li>
                            <li class="f2">其他</li>
                            <li class="f9">
                                <span>个人照片</span><input name="uploadtemp" type="file" class="on">  
                            </li> 
                            <li class="f4"></li>
                      	</ul>
                        <ul>
                            <li class="f1"></li>
                            <li class="f2">资质证书扫描件</li><li class="morepic">
                            <ul class="f9" id="otherpics"> 
                            </ul> <a href="#">+ 增加图片</a></li> 
                      	</ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f10">带有<strong>*</strong>标记的选项为必填。</li>
                            <li class="f3"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul><h5>可做项目</h5></ul>
             
                        <?php
                        foreach($items as $k)
						{
							if($k['pid']==0)
							{
								echo '<ul><li class="f1"></li>
                            <li class="f7">'.$k['name'].'</li><li class="f8">';
							foreach($items as $r){
								if($r['pid']==$k['id'])
								echo '<input name="items['.$r['id'].']" type="checkbox" value="'.$r['id'].'">'.$r['name'] ;
							  }
							  echo '</li></ul>';
							} 
							
						}
                        
                        ?>
                  
                        <ul><li class="f11"><input id="post_submit_button" name="post_submit_button"  type="submit" value=""> </li></ul>
                        
                    </div></form> 
                     <script type="text/javascript">
  function newgdcode() {
				var verify=document.getElementById("wenvalidecode");
				verify.setAttribute("src","<?php echo site_url('checkcode/G') ?>?ts"+Math.random());
 }  
            /* <![CDATA[ */
            jQuery(function(){
                jQuery("#Fnamerequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#Lnamerequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#introduce").validate({
                    expression: "if (VAL.length>12) return true; else return false;",
                    message: "请输入至少12个字符"
                });jQuery("#tel").validate({
                    expression: "if (VAL.length>5 && VAL[0]!=1) return true; else return false;",
                    message: "请输入正确电话"
                });
				  jQuery("#skilled").validate({
                    expression: "if (VAL.length>6) return true; else return false;",
                    message: "请输入至少6个字符"
                });
				; jQuery("#province").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "城市信息没有选择"
                }); 
				jQuery("#emailcheck").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                
            });
            /* ]]> */ 
 </script>
                     
                    <div class="clear" style="clear:both;"></div> 
                </div>
                        </div>
                    </div>  
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 