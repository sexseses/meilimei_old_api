<div class="page_content927">
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" >
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.insert.js"></script>
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" >
 <div class="institutions_info"><?php echo form_open_multipart("",array('id' => 'reg'))?>
<script type="text/javascript">
    $(function(){
        $("#otherpics").easyinsert();
        $("#anlipics").easyinsert({name: "anli"});
    });
</script> 
                	<div class="institutions_info_left">
                    	<h5>专家注册 <?php if(!$oncetime):?> <div style="padding:2px 10px; background:#fff;width:100px;font-size:12px;display:inline; border:dashed 1px #CCCCCC;"><a style="color:#2A98E5;" href="<?php echo site_url('user/logout') ?>">退出注册流程</a></div><?php endif; ?></h5>
                    	<ul><li class="f1">*</li><li class="f2">类型</li>
                        <li class="f3">
                        <select name="utypes" id="utypes">
                          <option value="">整形</option>
                          <option value="口腔">口腔</option>
                          <option value="瘦身纤体">瘦身纤体</option>
                          <option value="彩妆">彩妆</option>
                          <option value="美甲">美甲</option>
                          <option value="美发">美发</option>
                          <option value="美容/SPA">美容/SPA</option>
                          <option value="瑜伽">瑜伽</option>
                          <option value="舞蹈">舞蹈</option>
                          <option value="摄影写真">摄影写真</option>
                          <option value="美睫">美睫</option>
                        </select>	 
                        </li></ul>
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
                            <li class="f2">性别</li>
                            <li class="f8"><input name="sex" type="radio" value="1">女<input name="sex" type="radio" value="2" checked="checked">男</li>
                            <li class="f4"> </li>
                        </ul>
                        <ul class="zhengxing">
                        	<li class="f1"></li>
                            <li class="f2">职称</li>
                            <li class="f3"><input name="position" value="<?php echo isset($historydata['position']) && !empty($historydata)?$historydata['position']:'' ?>" type="text"></li>
                            <li class="f4">（博士、主任、教授等）</li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f2">固定电话</li>
                            <li class="f3"><input name="tel" id="tel" value="<?php echo isset($historydata['tel']) && !empty($historydata)?$historydata['tel']:'' ?>" type="text"></li>
                            <li class="f4">（可回答用户咨询）</li>
                        </ul> <ul>
                        	<li class="f1"> </li>
                            <li class="f2">手机号码</li>
                            <li class="f3"><input name="phone" value="<?php if(isset($historydata['phone'])) { echo $phone!=''?$phone:(!empty($historydata)?$historydata['phone']:'');}else if($phone) { echo $phone;} ?>" <?php  if($phone){echo 'readonly="readonly"';} ?>  type="text"></li>
                            <li class="f4">（可回答用户咨询）</li>
                        </ul><ul>
                        	<li class="f1"> </li>
                            <li class="f2">咨询手机</li>
                            <li class="f3"><input name="assistphone" value="<?php echo isset($historydata['assistphone']) && !empty($historydata)?$historydata['assistphone']:'' ?>" type="text"></li>
                            <li class="f4">（如有专人负责医师咨询，请输入TA的手机号）</li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">Email</li> 
                            <li class="f3"><input name="email" id="emailcheck" value="<?php echo (isset($email))?$email:( $historydata['email'] && !empty($historydata)?$historydata['email']:'');  ?>" <?php  if(isset($email) && $email){echo 'readonly="readonly"';} ?> type="text"></li>
                            <li class="f4"></li>
                        </ul> 
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">个人简介</li>
                            <li class="f6"><textarea name="introduce"  id="introduce"><?php echo isset($historydata['introduce']) && !empty($historydata)?$historydata['introduce']:'' ?></textarea></li>
                        </ul>
                         <ul class="zhengxing">
                        	<li class="f1">*</li>
                            <li class="f2">医师执业证号</li>
                            <li class="f6"><input name="yishi_sn" value="<?php echo isset($historydata['yishi_sn']) && !empty($historydata)?$historydata['yishi_sn']:'' ?>" type="text"></li>
                        </ul>
                        <ul class="zhengxing">
                        	<li class="f1">*</li>
                            <li class="f2">擅长领域</li>
                            <li class="f6"><textarea name="skilled" id="skilled" ><?php echo isset($historydata['skilled']) && !empty($historydata)?$historydata['skilled']:'' ?></textarea></li>
                            <li class="f4-1">（请详细描述）</li>
                        </ul>
                        <ul class="zhengxing">
                        	<li class="f1"> </li>
                            <li class="f7">所在科室<br>(可多选)</li>
                            <li class="f8"><?php foreach($keshi as $k=>$val){
								echo '<input name="department['.$k.']" type="checkbox" '.(isset($historydata['department']) && !empty($historydata) && is_int(strpos($historydata['department'],",$k,"))?'checked="checked"':'').' value="'.$k.'" class="selected">'.$val;
								} ?> </li>
                            <li class="f4"></li>
                        </ul>
                        <ul>
                        	<li class="f1">*</li>
                            <li class="f2">所属医院机构</li> <input type="hidden" id="companyid" value=""  disabled="disabled" /> 
                            <li class="f3"><input id="company" name="company" type="text" value="<?php echo isset($historydata['company']) && !empty($historydata)?$historydata['company']:'' ?>"></li>
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
                            <li class="f3"><input class=":min_length;4 " <?php echo !empty($historydata)?$historydata['address']:'' ?> style="width:100px" id="address" name="address" type="text"></li>
                        </ul>
                        
           <script type="text/javascript">
$(function(){ 
    $("#utypes").change(function(){ 
		if($(this).val().length==0){
			$(".zhengxing").show();
		}else{
			$(".zhengxing").hide();
		} 
    }); 
	$(".cotuselect").citySelect({
    	prov:"<?php echo !empty($historydata)?$historydata['province']:'' ?>", 
    	city:"<?php echo !empty($historydata)?$historydata['city']:'' ?>",
		dist:"<?php echo !empty($historydata)?$historydata['district']:'' ?>",
		nodata:"none",required:false, 
	});  
	 
});
</script>
                     
                        
                        <ul>
                            <li class="f1"></li>
                            <li class="f2">其他</li>
              <li class="f9 ">
  <span style="width:150px">个人照片   <a style="display:inline-block; border:solid 1px #CCCCCC;line-height:25px; padding:0px 2px; text-align:center; background:#EBFAF8;font-size:12px;width:70px;float:right; " href="<?php echo base_url('thumbplug') ?>?keepThis=true&TB_iframe=true&height=450&width=680" title="上传照片" class="thickbox">点击上传</a><img width="50px" src="<?php echo  'http://115.29.167.43/thumb/'.$uid.'_90' ?>" id="thumbpic" /></span><input name="uploadtemp" style="display:none;" type="file" class="on"> 
                            </li> 
                            <li class="f4"></li>
                      	</ul>
                        <ul  class="zhengxing">
                            <li class="f1"></li>
                            <li class="f2">医师执业证扫描件或照片</li>
                            <li class="morepic">
                                <ul class="f9" id="otherpics"></ul>
                                <a href="#">+ 增加图片</a>
                            </li>
                      	</ul>
                        <ul class="zhengxing">
                            <li class="f1"></li>
                            <li class="f2">案例图集</li>
                            <li class="morepic">
                                <ul class="f9" id="anlipics"></ul>
                                <a href="#">+ 增加图片</a>
                            </li>
                        </ul>
                        <ul>
                        	<li class="f1"> </li>
                            <li class="f10">带有<strong>*</strong>标记的选项为必填。</li>
                            <li class="f3"></li>
                            <li class="f4"></li>
                        </ul>
                        <ul class="zhengxing"><h5 id="moreitems">可做项目<font style="font-size:12px;">(+点击展开或关闭更多选项)</font></h5></ul>
             
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
                        <ul>
                        	<li class="f1"></li>
                            <li class="f2">输入验证码</li>
                            <li class="f12"><input id="validecode"  name="validecode" value="" type="text"></li>
                            <li class="f13"><img id="wenvalidecode" src="<?php echo site_url('checkcode/G').'?'.time() ?>" /></li>
                            <li class="f14"><a style=" cursor:pointer;" onclick="javascript:newgdcode();" >点击换一张</a></li> 
                        </ul> 
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
                }); 
				jQuery("#company").validate({
                    expression: "if (VAL.length>2) return true; else return false;",
                    message: "请输入至少2个字符"
                });jQuery("#address").validate({
                    expression: "if (VAL.length>4) return true; else return false;",
                    message: "请输入至少5个字符"
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
			 
 </script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script>
                    <div class="institutions_info_right">
                    	<ul>
                        	<li><span>我们专业服务于整形美容行业，收录了全国知名的整形美容医院和机构，为您的医院带来更多更精准的潜在客户。</span></li>
                            <li>免费，是的，此次医院信息的提交<span>完全免费</span>，提交后即会显示在我们的网络和移动终端上。<br>花上几分钟填写，就能快速的让全国的用户（特别你们所在城市）看到相关的整形信息。</li>
                            <li><span>如遇到问题，请与我们联系：</span><br>邮箱：hi@MeiLiZhenSuo.com <br>Q Q：747242966 <br />电话：400-6677-245
</li>
                        </ul><?php echo form_open("info/question",array('id' => 'sendqus'))?>
                        <ul>
                        <li>联系我们<br /><textarea id="remark"  cols="32" name="remark" rows="5"></textarea><br /><input id="sendcontact" style="padding:3px 5px" type="button" value="发送" /></li> 
                        </ul></form>
                    </div>
                    <div class="clear" style="clear:both;"></div> 
                </div>
            </div>
		</div>
        <div class="notice_info">
        	<ul>
            	<li>
                	<img src="http://static.meilimei.com.cn/public/images//icon1.png" width="95" height="95">
                	<h2>害羞到整形机构咨询</h2>
                    <h5>手机使用美丽诊所直接<br><span>线上咨询</span><br>你身边的整形医师</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images//icon2.png" width="95" height="95">
                	<h2>想了解更多的美容<br>整形信息</h2>
                    <h5><span>美丽</span>诊所涵盖全面的<span>资讯</span><br>让你在指尖私密地阅览<br>整形介绍和价格等</h5>
                </li>
                <li>
                	<img src="http://static.meilimei.com.cn/public/images//icon3.png" width="95" height="95">
                	<h2>分不清哪家整形医院<br>或医师更好</h2>
                    <h5>得益于一套<span>公平中立的评价体系</span><br>我们让你看到整形行业<br>真实的那一面</h5>
                </li>
                <li class="on">
                	<img src="http://static.meilimei.com.cn/public/images//icon4.png" width="95" height="95">
                	<h2>想找人聊聊变美的<br>心得和效果</h2>
                    <h5>美丽诊所专注于爱美之人的圈子<br>让大家私密而无所忌惮地<br><span>讨论美丽</span></h5>
                </li>
            </ul>
        </div> <script type="text/javascript">
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
    old_tb_remove();  $("#thumbpic").attr("src","<?php echo 'http://115.29.167.43/thumb/'. $this->wen_auth->get_user_id() . '_90?ts=' ?>"+Math.random());
    };
</script>
