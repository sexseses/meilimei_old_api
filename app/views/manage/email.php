<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/setting'); ?>">站点配置</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/setting/email'); ?>">邮件模板</a></li> 
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%">id</li>  
                                    <li style="width:20%">类型</li> 
                                    <li style="width:55%">标题</li> 
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){ 
									echo '<ul>
                                	<li style="width:10%">'.$row->id.'</li> <li style="width:20%">'.$row->type.'</li> 
                                  	<li style="width:55%">'.$row->title.'</li> 
                                   <li style="width:10%"> <a href="'.base_url('manage/email/detail/'.$row->id).'" >编辑</a></li>
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
