<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"> 
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo  site_url('manage/users'); ?>">普通用户管理</a></li> <li ><a href="<?php  echo site_url('manage/users/today'); ?>" >今日联系</a></li><li><a href="<?php echo site_url('manage/users/sendsms'); ?>">短信通知</a></li><li><a href="<?php echo site_url('manage/users/total'); ?>">统计</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/users/export'); ?>">导出</a></li>  </ul>
                        </div> <div class="manage_yuyue" >
                        <div class="manage_yuyue_form" >
                        <div class="clear" style="clear:both;">  
</div><?php echo form_open('') ?><ul> <li style="display:block;height:30px;width:100%;"><label>选择日期: </label>
<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker">
</li>
  
<li class="clear" style="display:block;padding:2px;width:100%;"><button id="message_submit" name="commit" class="button1" type="submit"><span><span> 导出 </span></span></button></li> 
 </ul><form><script type="text/javascript">
                    $(function(){ 
  $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();   
					})
                    </script> </div></div>
                    </div> 
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
