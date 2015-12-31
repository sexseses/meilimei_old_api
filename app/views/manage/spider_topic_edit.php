<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">.manage_yuyue_form li{display:block;clear:both;}</style>
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/spider'); ?>">机构临时数据</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/spider/topic'); ?>">话题临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gjigou'); ?>">抓取机构</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gtopic'); ?>">抓取话题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form"> 
                          <?php echo form_open_multipart('manage/spider/topicEdit/'.$contentid); ?> 
 <ul style="padding:10px;"><li style="padding:10px;"><label style="width:100px; display:inline-block">发布人</label><input id="user_id" name="tuser_id" type="text"  style="padding:2px;" value="" size="45" /><input type="hidden" id="suser_id" name="suser_id" value="" /></li>
 <li  style="padding:10px;"><label style="width:100px; display:inline-block">标题*</label><input name="title" type="text"  style="padding:2px;" value="<?php echo $res[0]['title'] ?>" size="45" /></li>
 <li  style="padding:10px;"><label style="width:100px; display:inline-block">标签*</label><input name="position" type="text"  style="padding:2px;" value="<?php echo $tags ?>" size="45" /></li>
  <?php $type = $this->db->query("select * from items")->result_array(); 
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
  ?>
 
  <li style="padding:10px;"><label style="width:100px; display:inline-block">内容*</label><textarea name="description" style="width:500px"><?php echo $topic ?></textarea></li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">上传配图</label><input type="file" name="uppics" /> </li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">配图</label> <?php echo isset($topic_pics[0][0])?$topic_pics[0][0]:'无'?> </li>
  <li style="padding:10px;"><label style="width:100px; display:inline-block">配图地址</label><input name="topics" type="text" value="<?php echo isset($topic_pics[1][0])?$topic_pics[1][0]:''?>"  type="text"  style="padding:2px;"  size="45"/>  </li>
  
 <li>==========================回帖========================</li>
 <?php foreach($replys as $r){
	 echo '<li style="padding:10px;"> <input name="replys[]" type="text"  style="padding:2px;width:600px" value="'.$r.'" size="45" /></li>';
 } 
 ?> 
<li style="padding:10px;"><label style="width:100px; display:inline-block">分配回答用户(,分隔)</label><textarea name="uids">6365,6094</textarea></li>
                   
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="通过" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
                                <div class="clear" style="clear:both;"></div>
                            </div> 
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div><script>
  $(function() { 
	$("#user_id").autocomplete({
                source: "../../topic/Suser",
                minLength: 2,
                select: function(event, ui) { 
                    $('#suser_id').val(ui.item.id);  
                }
 });
 $( "#ctime" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });
 
  </script>
  </div>
</div>
