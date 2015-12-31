<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo base_url('manage/pages/')?>">页面管理</a></li><li><a href="<?php echo base_url('manage/pages/add')?>">添加页面</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%">id</li>   
                                    <li style="width:75%">标题</li> 
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){ 
									echo '<ul>
                                	<li style="width:10%">'.$row->id.'</li> 
                                  	<li style="width:75%">'.$row->title.'</li> 
                                   <li style="width:10%"> <a href="'.base_url('manage/pages/edit/'.$row->id).'" >编辑</a></li>
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
