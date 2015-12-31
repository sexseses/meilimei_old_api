<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
            	<div class="institutions_info new_institutions_info">
                	<?php  $this->load->view('manage/leftbar'); ?>
                    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected"><a href="<?php echo site_url('manage/users/detail/'.$uid) ?>">用户基本资料</a></li><li><a href="<?php echo site_url('manage/users/editpass/'.$uid) ?>">修改密码</a></li><li><a href="<?php echo site_url('manage/users/follow/'.$uid) ?>">关注</a></li></a>
                                <li><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">跟踪纪录</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php
						 $attributes = array('id' => 'usersubmit'); 
						 echo form_open_multipart("manage/users/update",$attributes); ?>
                        	<ul>
                            	<li>
                                	<div class="k1"><span>*</span>个人照片：</div>
                                     <div class="k2"> <p><img id="thumbPic" src="<?php echo $thumb.'?'.time() ?>" width="120" ></p><input type="file" name="thumb" /> </div>
                                </li>
                            	<li><input class="inputbox" type="hidden" name="utype" value="1" /><input type="hidden" name="uid" value="<?php echo $uid ?>" />
                                	<div class="k1"><span>*</span>姓：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="Lname" name="Lname" value="<?php echo $userinfo[0]['Lname'] ?>" /></p></div>
                                </li>
                               
                                <li>
                                	<div class="k1"><span>*</span>名：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="Fname" name="Fname" value="<?php echo $userinfo[0]['Fname'] ?>" /></p></div>
                                </li>
                                 <li><input class="inputbox" type="hidden" name="utype" value="1" /><input type="hidden" name="uid" value="<?php echo $uid ?>" />
                                	<div class="k1"><span>*</span>昵称：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" id="alias" name="alias" value="<?php echo $userinfo[0]['alias'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"> 性别：</div>
                                    <div class="k2"><span></span><p><input type="radio" value="0" <?php echo $userinfo[0]['sex']==0?'checked="checked"':'' ?> name="sex" />保密 <input type="radio" <?php echo $userinfo[0]['sex']==1?'checked="checked"':'' ?> value="1" name="sex" />女 <input name="sex" type="radio" <?php echo $userinfo[0]['sex']==2?'checked="checked"':'' ?> value="2" />男</p></div>
                                </li>
                                <li>
                                	<div class="k1"> 电话：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="tel" value="<?php echo $userinfo[0]['tel'] ?>" /> </p></div>
                                </li>
                               <li>
                                	<div class="k1"> 手机号码：</div>
                                    <div class="k2"><span></span><p><?php if($userinfo[0]['rev_phone']==1){?><input id="phone" class="inputbox" type="text"  name="phone" value="<?php echo $userinfo[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php echo $userinfo[0]['phone'] ?>" /><?php }else{ echo $userinfo[0]['phone']; ?> <?php } ?></p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="email" type="text" name="email" value="<?php echo $userinfo[0]['email'] ?>" /></p></div>
                                    <input class="inputbox" id="phone" type="hidden" name="sourceemail" value="<?php echo $userinfo[0]['email'] ?>" />
                                </li>
                                <li>
                                	<div class="k1"> 咨询情况：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="introduce" cols="50" rows="3" class="inputbox"><?php echo $userinfo[0]['introduce'] ?></textarea>
                                  </p></div>
                                </li> 
                                 <li>
                                	<div class="k1"><span>*</span>详细地址：</div>
                                    <div class="k2 cotuselect">
                                    	<span></span>
                                        <p>
                                            <select class="prov" name="province" id="province"></select>
                                            <select class="city" name="city" id="city"></select>
                                            <select class="dist" name="district" id="district"></select>
                                            <input class="inputbox" type="text" name="address" value="<?php echo $userinfo[0]['address'] ?>" />
                                        </p>
                                    </div>
                                 </li>
                                 <!--<li>
                                	<div class="k1"> 咨询情况：</div>
                                    <div class="k2">
                                    	<span></span><p> 
                                        <textarea name="cremark" cols="50" rows="3" class="inputbox"><?php echo $userinfo[0]['cremark'] ?></textarea>
                                        </p> </div>
                                </li>-->
                                 <li>
                                	<div class="k1"> 客服备注：</div>
                                    <div class="k2">
                                    	<span></span><p> 
                                        <textarea name="remark" cols="50" rows="3" class="inputbox"><?php echo $userinfo[0]['remark'] ?></textarea>
                                        </p> </div>
                                </li>
                                <!--<li>
                                	<div class="k1"> 下次联系时间：</div>
                                    <div class="k2">
 <span></span><p> 
 <input name="contactNext" cols="50" rows="3" class="inputbox datepicker" value="<?php echo $userinfo[0]['contactNext']?strtotime($userinfo[0]['contactNext']):'' ?>"> 
 </p> </div>
                                </li>-->
                                <li>
                                	<div class="k1"><span>*</span>联系状态：</div>
                                    <div class="k2">
                                    	<span></span><p> 
                                       <input type="radio" name="states" <?php echo $userinfo[0]['states']==0?'checked="checked"':'' ?>  value="0" />未联系
                                       <input type="radio" name="states" <?php echo $userinfo[0]['states']==1?'checked="checked"':'' ?> value="1" />已联系
                                        </p> </div>
                                </li>
                                <li>
                                	<div class="k1"> 设为管理员角色：</div>
                                    <div class="k2">
                                    	<span></span><p>  
                                       <input type="radio"  name="role_id" <?php echo $userinfo[0]['role_id']==16?'checked="checked"':'' ?>  value="16" />是
                                        <input type="radio"  name="role_id" <?php echo $userinfo[0]['role_id']==1?'checked="checked"':'' ?>  value="1" />否
                                        </p> </div>
                                </li>
                                <li>
                                	<div class="k1">用户信息完成：</div>
                                    <div class="k2">
                                    	<span></span><p><label><input <?php echo $userinfo[0]['state']==1?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="1" />已完成</label> <label><input <?php echo $userinfo[0]['state']==0?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="0" /> 未完成</label> (注:选择完成时,必须姓名 邮箱信息完整) </p> </div>
                                </li><input type="hidden" name="submittype" id="submittype" value="1" />                                 <li>
                                	<div class="k1"><span>*</span>注册时间：</div>
                                    <div class="k2">
                                    	<span></span><p><?php echo date('Y-m-d H:i:s',$userinfo[0]['created']) ?></p> </div>
                                </li><li>
                                <?php if($view){ ?>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;"  type="button" onclick="javascript:window.history.back(-1);" value="关闭" />  </div>
                                  <?php }else{ ?><div class="k1"> </div>
                                    <div class="k2"> <input id="normalsubmit" onclick="return false" style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="直接保存" />  
                                    <input id="specsubmit"  onclick="return false" style="font-size:14px;   padding:3px 5px;" type="submit" value="保存并填写跟踪" />  
                                   <?php
								   $tmp = $this->db->query("SELECT id FROM  `yuyue` where userby={$uid} ")->result_array();
								   if(!empty($tmp)){
								   ?>
                                    <a href="<?php echo site_url('manage/home/detail/'.$tmp[0]['id']) ?>" style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" type="button">客户记录</a> <?php }else{ ?>
 <a href="<?php echo site_url('manage/home/addyuyue/'.$uid) ?>" style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" type="button">添加预约</a>
                                    <?php } ?> 
                                     </div>
                                  <?php } ?>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div> <script type="text/javascript"> 
            $(function(){
				$('#specsubmit').bind('click',function(){  
				  $("#submittype").val(2);
                  $('#usersubmit').submit();
                });
				$('#normalsubmit').bind('click',function(){  
				  $("#submittype").val(1);
                  $('#usersubmit').submit();
                }); 
			})
 </script>   <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script><script type="text/javascript">
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
<script type="text/javascript">
    $(function(){
        $(".cotuselect").citySelect({
            prov:"<?php echo $userinfo[0]['province']==''?$userinfo[0]['city']:$userinfo[0]['province'] ?>",
            city:"<?php echo $userinfo[0]['city'] ?>",
            dist:"<?php echo $userinfo[0]['district'] ?>",
            nodata:"none",
            required:false
        });
 $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });   
    });
</script>