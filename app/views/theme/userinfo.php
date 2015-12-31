<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                       <ul> <li class="selected"><a href="<?php echo site_url('user/info') ?>">医师基本资料</a></li><li><a href="<?php echo site_url('user/zhengshu') ?>">证书扫描件</a></li><li><a href="<?php echo site_url('user/ysablum') ?>">案例图集</a></li><li ><a href="<?php echo site_url('user/changepass/2') ?>">修改密码</a></li></ul>
                        </div>
                        <div class="personal_information"><?php echo form_open("user/update"); ?>
                        	<ul><input class="inputbox" type="hidden" name="utype" value="2" />
                            	<li>
                                	<div class="k1"><span>*</span>头像照片：</div>
                                    <div class="k2"><span><a href="<?php echo site_url('thumb') ?>"  title="上传头像">立即上传</a></span><p><img id="thumbPic" src="<?php echo $yishi[0]['thumb'].'?'.time() ?>" width="120" ></p></div>
                                </li>
                            	<li>
                                	<div class="k1"><span>*</span>姓：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="Lname" type="text" name="Lname" value="<?php echo $yishi[0]['Lname'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>名：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="Fname" type="text" name="Fname" value="<?php echo $yishi[0]['Fname'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"> 性别：</div>
                                    <div class="k2"><span></span><p><input type="radio" value="0" <?php echo $yishi[0]['sex']==0?'checked="checked"':'' ?> name="sex" />保密 <input type="radio" <?php echo $yishi[0]['sex']==1?'checked="checked"':'' ?> value="1" name="sex" />女 <input name="sex" type="radio" <?php echo $yishi[0]['sex']==2?'checked="checked"':'' ?> value="2" />男</p></div>
                                </li>
                                <li>
                                	<div class="k1"> 职称：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="position" value="<?php echo $yishi[0]['position'] ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>电话：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="tel" name="tel" value="<?php echo $yishi[0]['tel'] ?>" /> </p></div>
                                </li> <li>
                                	<div class="k1"><span>*</span>手机号码：</div>
                                    <div class="k2"><span></span><p><?php if($yishi[0]['rev_phone']==1){?><input id="phone" class="inputbox" type="text"  name="phone" value="<?php echo $yishi[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php echo $yishi[0]['phone'] ?>" /><?php }else{ echo $yishi[0]['phone']; ?> <?php } ?></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="phone" type="text" name="email" value="<?php echo $yishi[0]['email'] ?>" /></p></div>
                                </li><input class="inputbox" type="hidden" name="sourceemail" value="<?php echo $yishi[0]['email'] ?>" />
                                <li>
                                	<div class="k1"><span>*</span>个人简介：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="introduce" cols="50" rows="3" class="inputbox"><?php echo $yishi[0]['introduce'] ?></textarea>
                                  </p></div>
                                </li>
                                <li>
                                	<div class="k1"> 擅长领域：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="skilled" cols="50" rows="3" class="inputbox"><?php echo $yishi[0]['skilled'] ?></textarea>
                                  </p></div>
                                </li>
                                <li>
                                	<div class="k1"> 所在科室：</div>
                                    <div class="k2"><span></span><p><?php foreach($keshi as $k=>$val){  
								echo '<label>  <input name="department['.$k.']" type="checkbox" '.(isset($keshidata[$k])?' checked="checked"':'').'  value="'.$k.'" class="selected">'.$val.'</label>';
								} ?> </p></div>
                                </li>
                                <li>
                                	<div class="k1"> 所属机构：</div>
                                    <div class="k2"><span></span><p><input type="hidden" name="companyid"  /><input  id="company" class="inputbox" type="text" name="company" style="padding:2px; width:230px;" value="<?php echo $yishi[0]['company'] ?>" /> <font style="font-size:11px;">（输入时,系统将自动完成推荐需要的医院机构）</font> </p></div>
                                </li>
                                <Li>
                               <div class="k1">可做项目： </div><div class="k2"><?php $itmes = explode(',',','.$yishi[0]['items']);  
                        foreach($items as $k)
						{
							if($k['pid']==0)
							{
								echo '<ul><li class="f1"></li>
                            <li class="f7">'.$k['name'].'</li><li class="f8">';
							foreach($items as $r){
								if($r['pid']==$k['id'])
								echo '<label><input name="items['.$r['id'].']" '.(array_search($r['id'],$itmes)?'checked="checked"':'').' type="checkbox" value="'.$r['id'].'">'.$r['name'].' </label>';
							  }
							  echo '</li></ul>';
							} 
							
						}
                        
                        ?></div>
             
                       
                                </Li>
                                  <li class="cotuselect">
                                	<div class="k1"><span>*</span>详细地址：</div>
                                    <div class="k2"><span></span><p><select class="prov" name="province" id="province"> 
                                </select><select class="city" name="city" id="city"></select><select class="dist" name="district" id="district"></select> <input class="inputbox" type="text" name="address" value="<?php echo $yishi[0]['address'] ?>" /> </p></div>
                                </li> <li>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div><script type="text/javascript">
$(function(){ 
	$(".cotuselect").citySelect({
    	prov:"<?php echo $yishi[0]['province']==''?$yishi[0]['city']:$yishi[0]['province'] ?>", 
    	city:"<?php echo $yishi[0]['city'] ?>",
		dist:"<?php echo $yishi[0]['district'] ?>",
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
                });jQuery("#tel").validate({
                    expression: "if (VAL.length>5 && VAL[0]!=1) return true; else return false;",
                    message: "请输入正确电话"
                });
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
            } 
			var options = {
		script:"<?php echo site_url('jquery/getsuggest') ?>?json=true&limit=6&",
		varname:"input",
		json:true,
		shownoresults:false,
		maxresults:6,
		callback: function (obj) { document.getElementById('companyid').value = obj.id; }
	};
	var as_json = new bsn.AutoSuggest('company', options);</script>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 