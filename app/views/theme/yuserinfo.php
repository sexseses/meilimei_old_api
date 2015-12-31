<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected"><a href="<?php echo site_url('user/info') ?>">医院机构基本资料</a></li><li><a href="<?php echo site_url('user/hetong') ?>">合同扫描件</a></li><!--<li><a href="<?php echo site_url('user/ablum') ?>">医院相册</a></li>--><li><a href="<?php echo site_url('user/changepass/3') ?>">修改密码</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open("user/update"); ?>
                        	<ul>
                            	 <li>
                                	<div class="k1"><span>*</span>医院代表图：</div>
                                    <div class="k2"><span><a href="<?php echo site_url('thumb') ?>" title="上传医院代表图">立即上传</a></span><p><img id="thumbPic" src="<?php echo $thumb.'?'.time() ?>" width="120" ></p></div>
                                </li>
                            	<li><input class="inputbox" type="hidden" name="utype" value="3" />
                                	<div class="k1"><span>*</span>医院名称：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="yiyuanname" name="name" value="<?php echo $companyinfo[0]['name'] ?>" /></p></div>
                                </li> 
                                <li><input class="inputbox" type="hidden" name="utype" value="3" />
                                	<div class="k1"><span>*</span>联系人：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="contactN" name="contactN" value="<?php echo $companyinfo[0]['contactN'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>电话：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="tel" name="tel" value="<?php echo $companyinfo[0]['tel'] ?>" /> </p></div>
                                </li>
                                 <li>
                                	<div class="k1"><span>*</span>手机号码：</div>
                                    <div class="k2"><span></span><p><?php if($companyinfo[0]['rev_phone']==1){?><input class="inputbox" type="text"  name="phone" value="<?php echo $companyinfo[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php echo $companyinfo[0]['phone'] ?>" /><?php }else{ echo $companyinfo[0]['phone']; ?> <?php } ?></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="email" type="text" name="email" value="<?php echo $companyinfo[0]['email'] ?>" /></p></div>
                                </li><input class="inputbox" type="hidden" name="sourceemail" value="<?php echo $companyinfo[0]['email'] ?>" />
                                <li>
                                	<div class="k1"><span> </span>网站：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="web" value="<?php echo $companyinfo[0]['web'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span> </span>微博：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="weibo" value="<?php echo $companyinfo[0]['weibo'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span> </span>简介：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="descrition" cols="50" rows="3" class="inputbox"><?php echo $companyinfo[0]['descrition'] ?></textarea>
                                  </p></div> 
                                </li>
                                <li>
                                	<div class="k1"><span> </span>医师：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="users" value="<?php echo $companyinfo[0]['users'] ?>" /> </p>(名字使用,分隔)</div>
                                </li>
                                <li><div class="k1">参考报价（选填，但完整的价格能更好地推荐到用户端,填写纯数字）</div>
                        <div class="k2">
                        <?php  
                        foreach($items as $k)
						{
							if($k['pid']==0)
							{
								echo '<ul><li class="f1"></li>
                            <li class="f7">'.$k['name'].'</li><li class="f8"><ul>';
							foreach($items as $r){
								if($r['pid']==$k['id'])
								echo '<li><span>'.$r['name'].'</span><input name="items['.$r['id'].']" type="text" value="'.(isset($prices[$r['id']])?$prices[$r['id']]:'').'">元</li>' ;
							  }
							  echo '</ul></li></ul>';
							}  
						} 
                        ?></div>
                                <li>
                                	<div class="k1"><span> </span>营业时间：</div><input type="hidden" name="companyid" value="<?php echo $companyinfo[0]['id'] ?>" />
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="shophours" value="<?php echo $companyinfo[0]['shophours'] ?>" /> </p></div>
                                </li>
                                 <li class="cotuselect">
                                	<div class="k1"><span>*</span>详细地址：</div>
                                    <div class="k2"><span></span><p><select class="prov" name="province" id="province"> 
                                </select><select class="city" name="city" id="city"></select><select class="dist" name="district" id="district"></select> <input class="inputbox" type="text" name="address" value="<?php echo $companyinfo[0]['address'] ?>" /> </p></div>
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
    	prov:"<?php echo $companyinfo[0]['province']==''?$companyinfo[0]['city']:$companyinfo[0]['province'] ?>", 
    	city:"<?php echo $companyinfo[0]['city'] ?>",
		dist:"<?php echo $companyinfo[0]['district'] ?>",
		nodata:"none"
	});  
	 
});
</script> <script type="text/javascript"> 
            /* <![CDATA[ */
            jQuery(function(){
                jQuery("#contactN").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });
				jQuery("#yiyuanname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "不能为空"
                });jQuery("#tel").validate({
                    expression: "if (VAL.length>5 && VAL[0]!=1) return true; else return false;",
                    message: "请输入正确的电话"
                })
				jQuery("#email").validate({
                     expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9_]+(\\.[a-zA-Z0-9_]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                    message: "邮箱格式不正确,例如:user@meilizhensuo.com"
                });
                
            });
            /* ]]> */ 
 </script>  <script type="text/javascript" src="<?php echo base_url() ?>public/js/thickbox-compressed.js"></script><script type="text/javascript">
			var original_tb_remove = tb_remove;
			tb_remove = function(){ 
			 original_tb_remove(); return false;	
            } </script>       <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 