<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
 <ul> <li class="on"><a href="<?php echo base_url('manage/priv/')?>">管理员权限管理</a></li> 
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%">id</li>   
                                    <li style="width:65%">用户</li> 
                                    <li style="width:20%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($res as $row){ 
									echo '<ul>
                                	<li style="width:10%">'.$row->id.'</li> 
                                  	<li style="width:65%">'.$row->alias.'</li> 
                                   <li style="width:20%"> <a href="'.base_url('manage/priv/edit/'.$row->id).'" >编辑权限</a> <a href="'.base_url('manage/users/detail/'.$row->id).'" >编辑信息</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?>  
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
