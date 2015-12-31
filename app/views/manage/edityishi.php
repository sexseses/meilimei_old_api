<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/AutoSuggest_2.1.3_comp.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script> <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" ><div class="page_content933"> 
            	<div class="institutions_info">
                	<?php  $this->load->view('manage/leftbar'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul> 
                           <li class="selected"><a href="<?php echo site_url('manage/yishi/detail/'.$uid) ?>">医师基本资料</a></li> <li><a href="<?php echo site_url('manage/yishi/editpass/'.$uid) ?>">修改密码</a></li>  <li><a href="<?php echo site_url('manage/yishi/yishi_comment/'.$uid) ?>">评价</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"><?php echo form_open_multipart("manage/yishi/update"); ?>
                        	<ul><input class="inputbox" type="hidden" name="utype" value="2" />
                            	<li><input type="hidden" name="uid" value="<?php echo $uid ?>" />
                                	<div class="k1">头像照片：</div>
                                    <div class="k2"> <p><img id="thumbPic" src="<?php echo $yishi[0]['thumb'].'?'.time() ?>" width="120" ></p><input type="file" name="thumb" /> </div>
                                </li>
                            	<li>
                                	<div class="k1">姓：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="Lname" type="text" name="Lname" value="<?php echo $yishi[0]['Lname'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1">评论数：</div> 	
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysreplys" type="text" name="sysreplys" value="<?php echo $yishi[0]['sysreplys'] ?>" /></p></div>
                                </li> 
                                <li>
                                	<div class="k1">评分：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysgrade" type="text" name="sysgrade" value="<?php echo $yishi[0]['sysgrade'] ?>" /></p></div>
                                </li>
                                <li>
                                    <div class="k1">项目：</div>
                                    <div class="k2">
                                        <select name="item">

                                            <option value="全部">全部</option>
                                            <?php
                                            $this->db->where('isrecommend',1);
                                            $rs = $this->db->get('new_items')->result_array();
                                            if(!empty($rs)){
                                                foreach($rs as $i){?>
                                                    <?php if($i['name'] == $yishi[0]['item']){?>
                                                        <option value="<?php echo $i['name']?>" selected><?php echo $i['name']?></option>
                                                    <?php }else{?>
                                                        <option value="<?php echo $i['name']?>"><?php echo $i['name']?></option>
                                                    <?php }?>
                                            <?php }
                                            }?>
                                        </select>
                                    </div>
                                </li>
                                <li>
                                    <div class="k1">推荐：</div>
                                    <div class="k2"><span></span><p><label><input <?php echo $yishi[0]['isrecommend']==1?'checked="checked"':'' ?> class="inputbox" type="radio" name="isrecommend" value="1" />是</label> <label><input <?php echo $yishi[0]['isrecommend']==2?'checked="checked"':'' ?> class="inputbox" type="radio" name="isrecommend" value="0" /> 否</label></p></div>
                                </li>
                                <li>
                                    <div class="k1">排序：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="sort" type="text" name="sort" value="<?php echo $yishi[0]['sort'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1">投票数：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="sysvotenum" type="text" name="sysvotenum" value="<?php echo $yishi[0]['sysvotenum'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1">名：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="Fname" type="text" name="Fname" value="<?php echo $yishi[0]['Fname'] ?>" /></p></div>
                                </li>
                                <li>
                                	<div class="k1"> 性别：</div>
                                    <div class="k2"><span></span><p><input type="radio" value="0" <?php echo $yishi[0]['sex']==0?'checked="checked"':'' ?> name="sex" />保密 <input type="radio" <?php echo $yishi[0]['sex']==1?'checked="checked"':'' ?> value="1" name="sex" />女 <input name="sex" type="radio" <?php echo $yishi[0]['sex']==2?'checked="checked"':'' ?> value="2" />男</p></div>
                                </li>
                                <li>
                                	<div class="k1"> Tags：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="utags" value="<?php echo $yishi[0]['utags'] ?>" /> </p>用,分隔</div>
                                </li>
                                <li>
                                	<div class="k1"> 职称：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="position" value="<?php echo $yishi[0]['position'] ?>" /> </p></div>
                                </li>
                                <li>
                                	<div class="k1">电话：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" type="text" name="tel" value="<?php echo $yishi[0]['tel'] ?>" /> </p></div>
                                </li> <li>
                                	<div class="k1">手机号码：</div>
                                    <div class="k2"><span></span><p><?php if($yishi[0]['rev_phone']==1){?><input id="phone" class="inputbox" type="text"  name="phone" value="<?php echo $yishi[0]['phone'] ?>" /><input class="inputbox" type="hidden" name="sourcephone" value="<?php echo $yishi[0]['phone'] ?>" /><?php }else{ echo $yishi[0]['phone']; ?> <?php } ?></p></div>
                                </li>
                                <li>
                                	<div class="k1">Email：</div>
                                    <div class="k2"><span></span><p><input class="inputbox" id="phone" type="text" name="email" value="<?php echo $yishi[0]['email'] ?>" /></p></div>
                                    <input class="inputbox" id="phone" type="hidden" name="sourceemail" value="<?php echo $yishi[0]['email'] ?>" />
                                </li>
                                <li>
                                	<div class="k1">个人简介：</div>
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
                                	<div class="k1">详细地址：</div>
                                    <div class="k2"><span></span><p><select class="prov" name="province" id="province"> 
                                </select><select class="city" name="city" id="city"></select><select class="dist" name="district" id="district"></select> <input class="inputbox" type="text" name="address" value="<?php echo $yishi[0]['address'] ?>" /> </p> </div>
                                </li>

                                <li>

                                    <div class="k1">类别</div>
                                    <div class="k2">
                                        <select name="category">
                                            <option value="全部">全部</option>
                                            <option value="整形美容"　<?php if($yishi[0]['category'] === "整形美容"){ echo 'selected';} ?>>整形美容</option>
                                            <option value="皮肤美容" <?php if($yishi[0]['category'] === "皮肤美容"){ echo 'selected';} ?>>皮肤美容</option>
                                            <option value="微创美容" <?php if($yishi[0]['category'] === "微创美容"){ echo 'selected';} ?>>微创美容</option>
                                            <option value="丽人" <?php if($yishi[0]['category'] === "丽人"){ echo 'selected';} ?>>丽人</option>
                                        </select>
                                    </div>

                                </li>
                                <li>
                                	<div class="k1">用户信息完成：</div>
                                    <div class="k2">
                                    	<span></span><p><label><input <?php echo $yishi[0]['state']==1?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="1" />已完成</label> <label><input <?php echo $yishi[0]['state']==0?'checked="checked"':'' ?> class="inputbox" type="radio" name="state" value="0" /> 未完成</label> (注:选择完成时,必须姓名 邮箱信息完整) </p> </div>
                                </li><li>
                                	<div class="k1"> </div>
                                    <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
                                </li>
                                <div class="clear" style="clear:both;"></div>
                            </ul> <?php echo form_close(); ?>
                        </div>
                    </div> <script type="text/javascript">  
	$(function(){ 
	$(".cotuselect").citySelect({
    	prov:"<?php echo $yishi[0]['province']==''?$yishi[0]['city']:$yishi[0]['province'] ?>", 
    	city:"<?php echo $yishi[0]['city'] ?>",
		dist:"<?php echo $yishi[0]['district'] ?>",
		nodata:"none",
	});  
	 
}); 
 </script>  <script type="text/javascript"> 
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