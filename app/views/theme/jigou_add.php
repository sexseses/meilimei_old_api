j<div class="page_content927">
<link rel="stylesheet" type="text/css" href="http://pic.meilimei.com.cn/public/css/thickbox.css" >
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.insert.js"></script>
<script type="text/javascript" src="http://pic.meilimei.com.cn/public/js/jquery.validate.js"></script>
<script type="text/javascript">
$(function(){
	$("#otherpics").easyinsert();  
});
</script>
            	<div class="institutions_info"><?php echo form_open_multipart("",array('id' => 'reg'))?>
                	<div class="institutions_info_left">
                    	<h5>整形医院/机构信息录入  </h5>
                    	<ul>
                        	<li class="f1"> </li>
                            <li class="f2">医院名称</li>
                            <li class="f3"><input name="name" id="yiyuanname" value="<?php echo !empty($historydata)?$historydata['name']:'' ?>" type="text"></li>
                            <li class="f4">*</li>
                        </ul>
                        <ul>
                        <li class="f1">*</li>
                            <li class="f2">密码</li>
                            <li class="f3"><input name="password" id="passwordrequired" value="123456" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                            <li class="f1"> </li>
                            <li class="f7">团购</li>
                            <li class="f8"> 
                                <input type="checkbox" name="team" value="1">
                            </li>
                            <li class="f4"></li>
                        </ul> 
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">联系人</li>
                            <li class="f3"><input name="contactN" id="contactN" value="<?php echo !empty($historydata)?$historydata['contactN']:'' ?>" type="text"></li>
                            <li class="f4">*</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">电话</li>
                            <li class="f3"><input name="tel" id="tel" value="<?php echo !empty($historydata)?$historydata['tel']:'' ?>" type="text"></li>
                            <li class="f4">*（格式：如021-67898987）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">手机号码</li>
                            <li class="f3"><input name="phone" id="phone" value="<?php echo $phone!=''?$phone:(!empty($historydata)?$historydata['phone']:'');  ?>" <?php  if($phone){echo 'readonly="readonly"';} ?>  type="text"></li>
                            <li class="f4">*（可回答用户咨询）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">Email</li>
                            <li class="f3"><input name="email" id="emailcheck"  value="<?php echo $email;  ?>" <?php  if($email){echo 'readonly="readonly"';} ?> type="text"></li>
                            <li class="f4">*（可回答用户咨询）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">官方网址</li>
                            <li class="f3"><input name="web" class=":url" value="<?php echo !empty($historydata)?$historydata['web']:'' ?>" type="text"></li>
                            <li class="f4"></li>
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
                            <li class="f3"><input class=":min_length;4 " style="width:100px" id="address" value="<?php echo !empty($historydata)?$historydata['address']:'' ?>" name="address" type="text"></li>
                        </ul>
                       <script type="text/javascript">
$(function(){ 
	$(".cotuselect").citySelect({
    	prov:"<?php echo !empty($historydata)?$historydata['province']:'' ?>", 
    	city:"<?php echo !empty($historydata)?$historydata['city']:'' ?>",
		dist:"<?php echo !empty($historydata)?$historydata['district']:'' ?>",
		nodata:"none",required:false, 
	});  
	 
});
</script>
            
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">官方微博</li>
                            <li class="f3"><input name="weibo" value="<?php echo !empty($historydata)?$historydata['weibo']:'' ?>" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1"></li>
                            <li class="f2">医院简介</li>
                            <li class="f6"><textarea name="descrition" id="descrition" cols="" rows=""><?php echo !empty($historydata)?$historydata['descrition']:'' ?></textarea></li>
                            <li class="f4-1">*（200字以内） </li>
                        </ul>
                        <ul>
                        	<li class="f1"></li>
                            <li class="f2">下属医师 </li>
                            <li class="f6"><textarea name="users" cols="" rows=""><?php echo !empty($historydata)?$historydata['users']:'' ?></textarea></li>
                             <li class="f4-1">每个医师用小写逗号","分隔  </li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">营业时间</li>
                            <li class="f3"><input name="shophours" value="<?php echo !empty($historydata)?$historydata['shophours']:'' ?>" type="text"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f7">类别</li>
                            <li class="f3"><input type="text" name="tags" value="" /></li>
                            <li class="f4"></li>
                        </ul> 
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f7">科室/项目<br>(可多选)</li>
                            <li class="f8"><?php   foreach($keshi as $k=>$val){ 
								echo ' <input name="department['.$k.']" type="checkbox" '.(!empty($historydata) && is_int(strpos($historydata['department'],",$k,"))?'checked="checked"':'').' value="'.$k.'" class="selected">'.$val;
								} ?></li>
                            <li class="f4"></li>
                        </ul> 
                           
                        <ul><h5 id="moreitems"><font style="font-size:12px;">(+点击展开或关闭)</font> 参考报价（选填，但完整的价格能更好地推荐到用户端,填写纯数字）</h5></ul>
                        
                        <?php
                        foreach($items as $k)
						{
							if($k['pid']==0)
							{
								echo '<ul class="itemslists" style="display:none;"><li class="f1"></li>
                            <li class="f7">'.$k['name'].'</li><li class="f8"><ul>';
							foreach($items as $r){
								if($r['pid']==$k['id'])
								echo '<li><span>'.$r['name'].'</span><input name="items['.$r['id'].']" type="text" value="">元</li>' ;
							  }
							  echo '</ul></li></ul>';
							} 
							
						}
                        
                        ?>
                          
                        <ul>
                        	<li class="f11"><input id="post_submit_button" name="post_submit_button" value="" type="submit"></li>
                        </ul>
                    </div></form> <script type="text/javascript">
 function newgdcode() {
				var verify=document.getElementById("wenvalidecode");
				verify.setAttribute("src","<?php echo site_url('checkcode/G') ?>?ts"+Math.random());
				} 
   /* <![CDATA[ */
            jQuery(function(){
                jQuery("#yiyuanname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#passwordrequired").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#contactN").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#tel").validate({
                    expression: "if (VAL.length>5 && VAL[0]!=1) return true; else return false;",
                    message: "请输入正确电话"
                });
				jQuery("#phone").validate({
                    expression: "if (VAL.length>10) return true; else return false;",
                    message: "请输入正确手机"
                }); 
				jQuery("#emailcheck").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                jQuery("#descrition").validate({
                    expression: "if (VAL.length>6) return true; else return false;",
                    message: "请输入至少6个字符"
                }); jQuery("#address").validate({
                    expression: "if (VAL.length>4) return true; else return false;",
                    message: "请输入至少5个字符"
                });jQuery("#province").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "城市信息没有选择"
                }); 
				;jQuery("#validecode").validate({
                    expression: "if (VAL.length==4) return true; else return false;",
                    message: "请输入完整验证码"
                });$("#moreitems").click(function(){  
			 $(".itemslists").toggle(300);
			});$("#sendcontact").click(function(){  
			var remark=$('#remark').val();var token= $('input[name=wenhash]').val(); 
			$.post('<?php echo site_url('info/question'); ?>', {"remark":remark,"wenhash":token}, function(data) { 
             alert('已成功发送!'); $("#sendqus").remove();
	           })
			});
            });
            /* ]]> */ 
</script><script type="text/javascript" src="<?php echo base_url() ?>public/js/thickbox-compressed.js"></script>
                     
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div>
         <script type="text/javascript">
	 
	var old_tb_remove = window.tb_remove; 
    var tb_remove = function() {
    old_tb_remove();  $("#thumbpic").attr("src","<?php echo base_url() . 'images/users/' . $this->wen_auth->get_user_id() . '/userpic_thumb.jpg?ts=' ?>"+Math.random());
    };
</script>

        