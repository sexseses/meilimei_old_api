<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/appPush') ?>">推送任务</a></li><li><a href="<?php echo site_url('manage/appPush/add') ?>">添加任务</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:5%">ID</li>
                                    <li style="width:20%">标题</li>
                                    <li style="width:25%">内容</li>
                                    <li style="width:10%">发送频率</li>
                                    <li style="width:10%">时间</li> 
                                    <li style="width:15%">操作</li> 
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($results as $row){ 
								  echo ' <ul>
								    <li style="width:5%">'.$row->id .'</li>
                                	<li style="width:20%">'.$row->title .'</li>
									<li style="width:25%">'.$row->message .'</li> 
									<li style="width:10%">'.($row->datetype==1?'每天':'指定日期').'</li> 
                                    <li style="width:10%">'.date('Y-m-d',$row->cdate) .'</li>  
								    <li style="width:15%"><a onclick="return confirm(\'确认删除 '.$row->name.'?\')" href="'.site_url('manage/appPush/del/'.$row->id).'">删除</a></li> <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?>
                                
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                    <ul>
                                        <li><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div>
                            </div>
                        </div>
                    </div> 
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
