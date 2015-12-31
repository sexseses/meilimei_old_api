<div class="page_content927">
<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>public/css/thickbox.css" >
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.insert.js"></script>
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.validate.js"></script>
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script>
<link rel="stylesheet" type="text/css" href="http://pic.meilimei.com.cn/public/css/autosuggest_inquisitor.css" >
 <div class="institutions_info">
 <?php echo form_open_multipart("",array('id' => 'reg'))?>
<script type="text/javascript">
    $(function(){
        $("#otherpics").easyinsert();
        $("#anlipics").easyinsert({name: "anli"});
    });
</script> 

                	<div class="institutions_info_left">
                    	<h5>医师信息录入  </h5>
                    	<ul>
                        	<li class="f1">*</li>
                            <li class="f2">姓</li>
                            <li class="f3"><input name="Lname" id="Lnamerequired" value="<?php echo isset($historydata['Lname']) && !empty($historydata)?$historydata['Lname']:'' ?>" type="text"><?php echo form_error('Fname'); ?></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                   	    <li class="f1">*</li>
                            <li class="f2">名</li>
                            <li class="f3"><input name="Fname" id="Fnamerequired" value="<?php echo isset($historydata['Fname']) && !empty($historydata)?$historydata['Fname']:'' ?>" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                         <ul>
                        <li class="f1">*</li>
                            <li class="f2">密码</li>
                            <li class="f3"><input name="password" id="passwordrequired" value="123456" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">性别</li>
                            <li class="f8"><input name="sex" type="radio" value="1">女<input name="sex" type="radio" value="2" checked="checked">男</li>
                            <li class="f4"> </li>
                        </ul>
                        <ul>
                        	<li class="f1"></li>
                            <li class="f2">职称</li>
                            <li class="f3"><input name="position" value="" type="text"></li>
                            <li class="f4">（博士、主任、教授等）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">固定电话</li>
                            <li class="f3"><input name="tel" id="tel" value="" type="text"></li>
                            <li class="f4">（可回答用户咨询）</li>
                        </ul> <ul>
                        	<li class="f1"> </li>
                            <li class="f2">手机号码</li>
                            <li class="f3"><input name="phone" value="" type="text"></li>
                            <li class="f4">（可回答用户咨询）</li>
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
								echo '<input name="department['.$k.']" type="checkbox" '.(isset($historydata['department']) && !empty($historydata) && is_int(strpos($historydata['department'],",$k,"))?'checked="checked"':'').' value="'.$k.'" class="selected">'.$val;
								} ?> </li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">所属医院机构</li><input type="hidden" id="companyid" value="" disabled="disabled" /> 
                            <li class="f3"><input id="company" name="company" type="text" value="<?php echo isset($historydata['company']) && !empty($historydata) ?$historydata['company']:'' ?>"></li>
                            <li class="f4">（输入时,系统将自动完成推荐需要的医院机构）</li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">详细地址</li>
                            <li class="f5 cotuselect">
                               <select class="prov" name="province" id="province"> 
                                </select> <select class="city" name="city" id="city">
                                </select><select class="dist" name="district" id="district"> 
                                </select> 
                            </li>
                            <li class="f3"><input class=":min_length;4 " <?php echo !empty($historydata) ?$historydata['address']:'' ?> style="width:100px" id="address" name="address" type="text"></li>
                        </ul>


								
<script type="text/javascript">								
           $(function(){ 
       		$(".cotuselect").citySelect({
       	    	prov:"<?php echo !empty($historydata) ?$historydata['province']:'' ?>", 
       	    	city:"<?php echo !empty($historydata) ?$historydata['city']:'' ?>",
       			dist:"<?php echo !empty($historydata) ?$historydata['district']:'' ?>",
       			nodata:"none",required:false, 
       		});  
       	});
</script>

                        <ul>
                        	<li class="f1"> </li>
                            <li class="f10">带有<strong>*</strong>标记的选项为必填。</li>
                            <li class="f3"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul><h5 id="moreitems">可做项目<font style="font-size:12px;">(+点击展开或关闭更多选项)</font></h5></ul>
             
                        <?php
                        foreach($items as $k)
						{
							if($k['pid']==0)
							{
								echo '<ul class="itemslists" style="display:none;"><li class="f1"></li>
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
				 jQuery("#passwordrequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#Lnamerequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#introduce").validate({
                    expression: "if (VAL.length>12) return true; else return false;",
                    message: "请输入至少12个字符"
                }); 
				jQuery("#company").validate({
                    expression: "if (VAL.length>2) return true; else return false;",
                    message: "请输入至少2个字符"
                });jQuery("#address").validate({
                    expression: "if (VAL.length>4) return true; else return false;",
                    message: "请输入至少5个字符"
                });jQuery("#skilled").validate({
                    expression: "if (VAL.length>6) return true; else return false;",
                    message: "请输入至少6个字符"
                });
				;jQuery("#validecode").validate({
                    expression: "if (VAL.length==4) return true; else return false;",
                    message: "请输入完整验证码"
                });jQuery("#province").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "城市信息没有选择"
                }); 
				jQuery("#emailcheck").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                $("#moreitems").click(function(){  
			 $(".itemslists").toggle(300);
			});$("#sendcontact").click(function(){  
			 var remark=$('#remark').val();var token= $('input[name=wenhash]').val();
			  $.post('<?php echo site_url('info/question'); ?>', {"remark":remark,"wenhash":token}, function(data) {alert('已成功发送!');   $("#sendqus").remove();
	           })
			});
            });
            /* ]]> */ 
 </script>
 <script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/thickbox-compressed.js"></script>
                     
                    <div class="clear" style="clear:both;"></div> 
                </div>
            </div>
		</div>
          <script type="text/javascript">
	var options = {
		script:"<?php echo site_url('jquery/getsuggest') ?>?json=true&limit=6&",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:6,
		callback: function (obj) { document.getElementById('companyid').value = obj.id; }
	};
	var as_json = new bsn.AutoSuggest('company', options);
	var old_tb_remove = window.tb_remove; 
    var tb_remove = function() {
    old_tb_remove();  $("#thumbpic").attr("src","<?php echo base_url() . 'images/users/' . $this->wen_auth->get_user_id() . '/userpic_thumb.jpg?ts=' ?>"+Math.random());
    };
</script>
