<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo base_url('manage/category/')?>">项目管理</a></li><li><a href="<?php echo base_url('manage/category/add/'.$pid)?>">添加项目</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%">id</li>  
                                    <li style="width:10%">Pid</li> 
                                    <li style="width:35%">名称</li>  
                                     <li style="width:20%">排序</li>  
                                    <li style="width:20%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){ 
									echo '<ul>
                                	<li style="width:10%">'.$row->id.'</li> 
									<li style="width:10%">'.$row->pid.'</li> 
                                  	<li style="width:35%">'.$row->name.'</li> <li style="width:20%">'.$row->order.'</li> 
                                   <li style="width:20%"> 
									'.($row->name!='热门'?'<a href="'.base_url('manage/category/index/'.$row->id).'" >子项目</a>':'<a href="'.base_url('manage/category/sec/'.$row->id).'" >子项目</a>').' 
									<a href="'.base_url('manage/category/edit/'.$row->id).'" >编辑</a>  &nbsp;&nbsp;&nbsp;
									'.($row->name!='热门'?'<a onclick="return confirm(\'确认删除 '.$row->name.'?\')" href="'.base_url('manage/category/del/'.$row->id).'" >删除</a>':'').'</li>
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
