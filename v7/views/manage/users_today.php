<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo  site_url('manage/users'); ?>">普通用户管理</a></li> <li class="on"><a href="<?php  echo site_url('manage/users/today'); ?>" >今日联系</a></li><li><a href="<?php echo site_url('manage/users/sendsms'); ?>">短信通知</a></li><li><a href="<?php echo site_url('manage/users/total'); ?>">统计</a></li></ul>
                        </div> <div class="manage_yuyue" >
                        <div class="manage_yuyue_form" >
                      <ul> 
                                    <li style="width:10%">ID</li> 
                                    <li style="width:20%">名字</li> 
                                    <li style="width:15%">联系时间</li> 
                                    <li style="width:35%">备注</li> 
                                    <li style="width:10%">操作时间</li> 
                                    <li class="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($res as $row){
								  echo ' <ul >
                                	 <li style="width:10%">'.$row->vuid .'</li>
									 <li style="width:20%">'.$row->alias .'</li>
									 <li style="width:15%">'.$row->vuid .'</li>
									 <li style="width:35%">'.$row->vuid .'</li>
									 <li style="width:10%">'.$row->vuid .'</li>
									 <li class="width:15%"> <a href="'.site_url('manage/users/track/'.$row->vuid).'">跟踪</a></li>
									  <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?> <div class="clear" style="clear:both;"></div>
                            </div></div>
                    </div><script type="text/javascript">$(function() {
                    $(".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();  }) 
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
