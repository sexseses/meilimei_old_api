<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">.manage_yuyue_form li{display:block;clear:both;}</style>
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/spider'); ?>">机构临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/topic'); ?>">话题临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gjigou'); ?>">抓取机构</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gtopic'); ?>">抓取话题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                         
                        	<div class="manage_yuyue_form"> 
                          <?php echo form_open_multipart('manage/spider/jigouEdit/'.$contentid); ?> 
 <ul style="padding:10px;">
 <li  style="padding:10px;"><label style="width:100px; display:inline-block">名称*</label><input name="name" type="text"  style="padding:2px;" value="<?php echo $res[0]['title'] ?>" size="45" /></li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">密码*</label><input name="password" type="text"  style="padding:2px;" value="<?php echo rand(111111,999999) ?>" size="45" /></li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">联系人</label><input name="contactN" type="text"  style="padding:2px;" value="<?php echo $contactName ?>" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">邮箱*</label><input name="email" type="text"  style="padding:2px;" value="<?php echo time().rand(1111,9999) ?>@meilimei.com" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">上班时间</label><input name="shophours" type="text"  style="padding:2px;" value="<?php echo $shoptime ?>" size="45" /></li> 
<li style="padding:10px;"><label style="width:100px; display:inline-block">省份*</label><input name="province" type="text"  style="padding:2px;" value="<?php echo $city ?>" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">城市*</label><input name="city" type="text"  style="padding:2px;" value="<?php echo $city ?>" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">区域*</label><input name="district" type="text"  style="padding:2px;" value="<?php echo $dist ?>" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">地址*</label><input name="address" type="text"  style="padding:2px;" value="<?php echo $addr ?>" size="45" /></li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">水印</label><select name="shuiyinpos"><option value="1" selected="selected">正常</option>
  <option value="2">大众团</option></select></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">电话*</label><input name="tel" type="text"  style="padding:2px;" value="<?php echo $tel ?>" size="45" /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">类别*</label><input name="tags" type="text"  style="padding:2px;" value="<?php echo $deparmtent ?>" size="45" />","分隔</li>
 <li> <?php $type = $this->db->query("select * from items")->result_array(); 
function getson(&$type,$pid){ 
		 foreach($type as $k){
			 if($k['pid']==$pid){
				 $tmph.='<label style="padding-right:10px"><input type="checkbox" value="'.$k['name'].'" name="positions[]">'.$k['name'].'</label>';
				 $tmph.=getson($type,$k['id']);
			 } 
	     } 
		 return $tmph;
  }
  foreach($type as $r){
	  if($r['pid']==0){
		  echo '<li><label style="padding-right:10px"><input type="checkbox" value="'.$r['name'].'" name="positions[]">'.$r['name'].'</label>';
		  echo getson($type,$r['id']).'</li>';
	  }
    } 
  ?></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">科室</label><?php   foreach($keshi as $k=>$val){ 
								echo ' <input name="department['.$k.']" type="checkbox" '.(!empty($historydata) && is_int(strpos($historydata['department'],",$k,"))?'checked="checked"':'').' value="'.$k.'" class="selected">'.$val;
								} ?></li>

<textarea style="display:none" name="picurls"><?php 
 $tmp = array();
 foreach($plist['list'] as $r){
	 $tmp[]=str_replace('240c180','700c700',$r);
 } 
 echo serialize($tmp);
 ?></textarea> 

 <li><label style="width:100px; display:inline-block">图集地址</label>
 <?php  
 foreach($tmp as $r){
	 echo '<br>',$r ;
 }  
 ?>
 </li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">头像图片*</label><input name="thumburl" type="text"  style="padding:2px;" value="<?php echo $tmp[0] ?>" size="45" /></li>
    <li style="padding:10px;"><label style="width:100px; display:inline-block">上传头像</label><input name="upthumburl" type="file"  /></li>
 <li style="padding:10px;"><label style="width:100px; display:inline-block">描述*</label><textarea style="width:550px" name="descrition"></textarea> </li>
<li style="padding:10px;"><label style="width:100px; display:inline-block">上传图集</label> <input name="upintures[]" type="file"  /></li>  
<li><label style="width:100px; display:inline-block"></label><input name="upintures[]" type="file"  /></li><li><label style="width:100px; display:inline-block"></label><input name="upintures[]" type="file"  /></li><li><label style="width:100px; display:inline-block"></label><input name="upintures[]" type="file"  /></li><li><label style="width:100px; display:inline-block"></label><input name="upintures[]" type="file"  /></li><li><label style="width:100px; display:inline-block"></label><input name="upintures[]" type="file"  /></li>                        
  <li style="padding:10px;"><label style="width:100px; display:inline-block">图集</label> <?php 
 foreach( $tmp as $r){
	 echo '<img src="'.$r.'" />';
 }
 ?></li>                    <li><label style="width:100px; display:inline-block">发布设置</label>  <input type="radio" checked="checked" name="is_publish" value="1" style="padding:2px 10px;" />通过  <input type="radio" name="is_publish" value="0" style="padding:2px 10px;" />不通过</li> 
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="提交" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                                <div class="clear" style="clear:both;"></div>
                            </div> 
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript">$(function(){
    
    var numTd = $(".editamount"); 
    numTd.click(function() {     
        var tdObj = $(this);
        if (tdObj.children("input").length > 0) { 
            return false;
        }
        var text = tdObj.html();  
        tdObj.html(""); 
        var inputObj = $("<input type='text'>").css("border-width","0")
            .css("font-size","16px").width(tdObj.width())
            .css("background-color",tdObj.css("background-color"))
            .val(text).appendTo(tdObj); 
        inputObj.trigger("focus").trigger("select");
        inputObj.click(function() {
            return false;
        }); 
        inputObj.keyup(function(event){ 
            var keycode = event.which; 
            if (keycode == 13  ) { 
                var inputtext = $(this).val(); 
                tdObj.html(inputtext);
				sendamount(inputtext,tdObj.attr("data-id"));
            } 
            if (keycode == 27) { 
                tdObj.html(text);
				sendamount(inputtext,tdObj.attr("data-id"));
            }
        });
    });
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/tuijianset', {"dataid":dataid,"weight":amount}, function(data) {  
   });}</script>
  </div>
</div>
