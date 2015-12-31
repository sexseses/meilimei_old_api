<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.cityselect.js"></script><script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.validate.js"></script>
<div class="page_content937">
  <div class="institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li class="selected"><a href="<?php echo site_url('manage/yiyuan/detail/'.$uid) ?>">医院机构基本资料</a></li> <li><a href="<?php echo site_url('manage/yiyuan/editpass/'.$uid) ?>">修改密码</a></li> <li><a href="<?php echo site_url('manage/yiyuan/comment/'.$uid) ?>">评价</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open_multipart("manage/yiyuan/update"); ?>
                        	<ul>
                            	<li>
                                	<div class="k1"><span>*</span>医院代表图：</div>
                                    <div class="k2"> <p><img id="thumbPic" src="<?php echo $thumb.'?'.time() ?>" width="120" ></p><input type="file" name="thumb" /> </div>
                                </li><input type="hidden" name="uid" value="<?php echo $uid ?>" /><input class="inputbox" type="hidden" name="utype" value="3" />
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
                                	<div class="k1">评论数：</div> 	
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysreplys" type="text" name="sysreplys" value="<?php echo $companyinfo[0]['sysreplys'] ?>" /></p></div>
                                </li> 
                                <li>
                                	<div class="k1">评分：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysgrade" type="text" name="sysgrade" value="<?php echo $companyinfo[0]['sysgrade'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1">投票数：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysvotenum" type="text" name="sysvotenum" value="<?php echo $companyinfo[0]['sysvotenum'] ?>" /></p></div>
                                </li>
                                 <li>
                                	<div class="k1"><span>*</span>手机号码：</div>
                                    <div class="k2"><span></span><p> <input class="inputbox" type="text"  name="phone" value="<?php echo $companyinfo[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php echo $companyinfo[0]['phone'] ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1"><span>*</span>Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="email" type="text" name="email" value="<?php echo $companyinfo[0]['email'] ?>" /></p></div>
                                </li><input class="inputbox" type="hidden" name="sourceemail" value="<?php echo $companyinfo[0]['email'] ?>" />
                                <li>
                                	<div class="k1"><span> </span>类别：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="utags" value="<?php echo $companyinfo[0]['utags'] ?>" /></p></div>
                                </li>
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
                                	<div class="k1"><span> </span>备注：</div>
                                    <div class="k2"><span></span><p>
                                      <textarea name="remark" cols="50" rows="3" class="inputbox"><?php echo $companyinfo[0]['remark'] ?></textarea>
                                  </p></div> 
                                </li> 
                                <li style="<?php echo $uid==6105?'':'display:none'?>">
                                	<div class="k1"><span> </span>提成：</div>
                                    <div class="k2"><span></span><p>
                                    <input class="inputbox" type="text" name="rebate" value="<?php echo $companyinfo[0]['rebate'] ?>" /> 30%=>0.3
                                  </p></div> 
                                </li>
                                <li>
                                	<div class="k1"><span> </span>优惠折扣：</div>
                                    <div class="k2"><span></span><p>
                                    <input class="inputbox" type="text" name="coupon" value="<?php echo $companyinfo[0]['coupon'] ?>" /> 九八折=>0.98 
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
                                    <div class="k2">  
                         <p>国家<input name="country" id="country" value="<?php echo $companyinfo[0]['country'] ?>">  </p><p>
                                     <p>省<input name="province" id="province" value="<?php echo $companyinfo[0]['province']==''?$companyinfo[0]['city']:$companyinfo[0]['province'] ?>">  </p><p>
                              市 <input name="city" value="<?php echo $companyinfo[0]['city']==''?'北京':$companyinfo[0]['city'] ?>" id="city">
                             </p><p>  区 
                               <input name="district" id="district" value="<?php echo $companyinfo[0]['district'] ?>"> <input class="inputbox" type="text" name="address" value="<?php echo $companyinfo[0]['address'] ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1">信息完成：</div>
                                    <div class="k2">
                                    <span></span><p><label><input <?php echo $companyinfo[0]['state']==1?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="1" />已完成</label><label><input <?php echo $companyinfo[0]['state']==0?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="0" /> 未完成</label> (注:选择完成时,必须姓名 邮箱信息完整) </p> 
                                    </div>
                                </li> 
                                <li>
                                    <div class="k1"> 团购 </div>
                                    <div class="k2"> <input  type="checkbox" name="team" value="1" <?php echo $companyinfo[0]['team']==1?'checked="checked"':'' ?> />  </div>
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
	    
	 
});
</script><script type="text/javascript"> 
            /* <![CDATA[ */
            jQuery(function(){
                jQuery("#province").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: "城市信息没有选择"
                }); 
            });
            /* ]]> */ 
 </script>   
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
