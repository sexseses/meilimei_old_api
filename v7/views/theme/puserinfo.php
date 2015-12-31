<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.validate.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected"><a href="<?php echo site_url('user/info') ?>">用户基本资料</a></li><li><a href="<?php echo site_url('user/changepass/1') ?>">修改密码</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open("user/update"); ?>
                        	<ul>
                            	<li>
                                	<div class="k1"><span>*</span>个人照片：</div>
                                    <div class="k2"><span><a href="<?php echo site_url('thumb') ?>" title="上传头像">立即上传</a></span><p><img id="thumbPic" src="<?php echo $userinfo[0]['thumb'].'?'.time() ?>" width="120" ></p></div>
                                </li>
                            	<li><input class="inputbox" type="hidden" name="utype" value="1" />
                                	<div class="k1"><span>*</span>姓：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="Lname" name="Lname" value="<?php if(isset($userinfo[0]['Lname'])) {echo $userinfo[0]['Lname'];} ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>名：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="Fname" name="Fname" value="<?php if(isset($userinfo[0]['Fname'])) echo $userinfo[0]['Fname'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"> 性别：</div>
                                    <div class="k2"><span></span><p><input type="radio" value="0" <?php if(isset($userinfo[0]['sex']))  {echo $userinfo[0]['sex']==0?'checked="checked"':'' ;}?> name="sex" />保密 <input type="radio" <?php if(isset($userinfo[0]['sex'])) echo  $userinfo[0]['sex']==1?'checked="checked"':'' ?> value="1" name="sex" />女 <input name="sex" type="radio" <?php if(isset($userinfo[0]['sex'])) echo $userinfo[0]['sex']==2?'checked="checked"':'' ?> value="2" />男</p></div>
                                </li>
                                <li>
                                	<div class="k1"> 电话：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="tel" value="<?php if(isset($userinfo[0]['tel'])) echo $userinfo[0]['tel'] ?>" /> </p></div>
                                </li>
                               <li>
                                	<div class="k1"> 手机号码：</div>
                                    <div class="k2"><span></span><p><?php if(isset($userinfo[0]['rev_phone']) && $userinfo[0]['rev_phone']==1){?><input id="phone" class="inputbox" type="text"  name="phone" value="<?php if(isset($userinfo[0]['phone'])) echo $userinfo[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php if(isset($userinfo[0]['phone'])) echo $userinfo[0]['phone'] ?>" /><?php }else{ if(isset($userinfo[0]['phone'])) echo $userinfo[0]['phone']; ?> <?php } ?></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="email" type="text" name="email" value="<?php if(isset($userinfo[0]['email'])) echo $userinfo[0]['email'] ?>" /></p></div>
                                </li><input class="inputbox" type="hidden" name="sourceemail" value="<?php if(isset($userinfo[0]['email'])) echo $userinfo[0]['email'] ?>" />
                                <li>
                                	<div class="k1"> 个人简介：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="introduce" cols="50" rows="3" class="inputbox"><?php echo $userinfo[0]['introduce'] ?></textarea>
                                  </p></div>
                                </li>
                                <li class="cotuselect">
                                	<div class="k1"><span>*</span>详细地址：</div>
                                    <div class="k2"><span></span><p><select class="prov" name="province" id="province"> 
                                </select><select class="city" name="city" id="city"></select><select class="dist" name="district" id="district"></select> <input class="inputbox" type="text" name="address" value="<?php if(isset($userinfo[0]['address'])) echo $userinfo[0]['address'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div><script type="text/javascript">
$(function(){ 
	$(".cotuselect").citySelect({
    	prov:"<?php echo $userinfo[0]['province']==''?$userinfo[0]['city']:$userinfo[0]['province'] ?>", 
    	city:"<?php echo $userinfo[0]['city'] ?>",
		dist:"<?php echo $userinfo[0]['district'] ?>",
		nodata:"none"
	});  
	 
});
</script><script type="text/javascript"> 
            /* <![CDATA[ */
            jQuery(function(){
                jQuery("#Lname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#Fname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#phone").validate({
                    expression: "if (VAL.length>5) return true; else return false;",
                    message: "请输入至少6个字符"
                })
				jQuery("#email").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                
            });
            /* ]]> */ 
 </script><script type="text/javascript" src="<?php echo base_url() ?>public/js/thickbox-compressed.js"></script><script type="text/javascript">
			var original_tb_remove = tb_remove;
			tb_remove = function(){ 
			 original_tb_remove(); return false;	
            }</script><div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 