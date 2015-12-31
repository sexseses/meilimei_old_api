<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/magic'); ?>">魔镜管理</a></li>
                                </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:8%">ID</li> 
                                    <li style="width:18%">图片</li> 
                                    <li style="width:10%">魅力指数</li>
                                    <li style="width:15%">皮肤</li>
                                    <li style="width:10%">性别</li>
                                    <li style="width:10%">昵称</li>
                                    <li class="width:15%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($results as $row){
								  echo ' <ul class="">
                                	 <li style="width:8%">'.$row->id .'</li> 
                                     <li style="width:18%"><img width="150px" height="200px" src="http://pic.meilimei.com.cn/upload/'.$row->pic .'"/></li> 
                                    <li style="width:10%">'.$row->score .'</li>
                                    <li style="width:15%">'.$row->skins .'</li>
                                    <li style="width:10%">'.$row->phone .'</li> 
                                    <li style="width:10%">'.$row->alias.'</li>
								    <li class="width:15%"><a onclick="return confirm(\'确认删除 '.$row->id.'?\')" href="'.site_url('manage/magic/del/'.$row->id).'">删除</a></li> <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?>
                                
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                   <?php echo $pagelink ?>
                                </div>
                            </div>
                        </div>
                    </div><script type="text/javascript">
                    $(function(){ 
  $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();  
						$(".Vertical24").click(function(){ 
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
					 
					})
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
