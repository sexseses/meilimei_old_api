<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#" >站内信</a></li><li><a href="#">发送</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical17">ID</li>
                                    <li class="Vertical18">内容</li>
                                    <li class="Vertical19">时间</li> 
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($results as $row){
								  echo ' <ul>
                                	<li class="Vertical17">'.$row->name .'</li><li class="Vertical18">'.$row->contactN .'</li> 
                                    <li class="Vertical19">'.$row->tel .'</li> <li class="Vertical20">'.$row->phone .'</li>
                                    <li class="Vertical21">'.$row->email .'</li>  <li class="Vertical22">'.$row->city .'</li> 
									<li class="Vertical23"><a href="'.site_url('manage/yiyuan/detail/'.$row->id).'">查看</a></li> 
									<li class="Vertical21 hezuo" data-id="'.$row->id .'">'.($row->state?'是':'<a>否</a>').'</li>
								    <li class="Vertical24 ustates" data-id="'.$row->id .'">'.($row->banned?'禁用':'<a>正常</a>').'</li><li ><a onclick="return confirm(\'确认删除 '.$row->name.'?\')" href="'.site_url('manage/yiyuan/del/'.$row->id).'">删除</a></li> <div class="clear" style="clear:both;"></div>
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
                    </div><script type="text/javascript">
                    $(function(){ 
						$(".ustates").click(function(){ 
						    var _obj = $(this);
							if(_obj.text()=='正常'){  _obj.text('处理中'); 
								 $.get('<?php echo site_url('manage/yishi/userac'); ?>', {"uid":$(this).attr('data-id'),"banned":1}, function(data) {
									  _obj.text('禁用'); 
	                             })
							}else if(_obj.text()=='禁用'){ _obj.text('处理中');  
								$.get('<?php echo site_url('manage/yishi/userac'); ?>', {"uid":$(this).attr('data-id'),"banned":0}, function(data) {
									  _obj.html('<a>正常</a>'); 
                                     
	                             })
							}							
							});
						$(".hezuo").click(function(){ 
						    var _obj = $(this);
							if(_obj.text()=='是'){  _obj.text('处理中'); 
								 $.get('<?php echo site_url('manage/yiyuan/userac'); ?>', {"uid":$(this).attr('data-id'),"state":0}, function(data) {
									  _obj.text('否'); 
	                             })
							}else if(_obj.text()=='否'){ _obj.text('处理中');  
								$.get('<?php echo site_url('manage/yiyuan/userac'); ?>', {"uid":$(this).attr('data-id'),"state":1}, function(data) {
									  _obj.html('<a>是</a>'); 
                                     
	                             })
							}							
							});
					 
					})
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
