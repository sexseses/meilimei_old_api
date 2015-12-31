<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo base_url('manage/banner')?>">Banner管理</a></li><li><a href="<?php echo base_url('manage/banner/add')?>">添加Banner</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/yishi'); ?>
                            	<ul> 
                                    <li>标题<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>" maxlength="62"></li>  
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div> 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:5%">ID</li> 
                                    <li style="width:15%">标题</li><li style="width:10%">权重</li>
                                    <li style="width:20%">图片</li>
                                    <li style="width:15%">标签</li> <li style="width:15%">日期</li> 
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul> 
                             <?php 
								foreach($results as $row){
								  echo ' <ul>
                                	<li style="width:5%">'.$row->id .'</li><li style="width:15%">'.$row->title .'</li><li style="width:10%">'.$row->weigh .'</li>
                                    <li style="width:20%"><img style="max-height:100px; max-width:100px" src="../'.$row->picture .'"/></li> <li style="width:15%">'.$row->tags .'</li>
                                    <li style="width:15%">'.date('Y-m-d',$row->cdate) .'</li>  
								    <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->title.'?\')" href="'.site_url('manage/banner/del/'.$row->id).'">删除</a>  <a href="'.site_url('manage/banner/edit/'.$row->id).'">编辑</a></li>
									 <div class="clear" style="clear:both;"></div>
                                </ul>';	
								}							
								 ?>
                               
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
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
							$(".suggest").click(function(){ 
						    var _obj = $(this);
							if(_obj.text()=='未推荐'){  _obj.text('处理中'); 
								 $.get('<?php echo site_url('manage/yishi/suggest'); ?>', {"uid":$(this).attr('data-id'),"suggest":1}, function(data) {
									  _obj.text('已推荐'); 
	                             })
							}else if(_obj.text()=='已推荐'){ _obj.text('处理中');  
								$.get('<?php echo site_url('manage/yishi/suggest'); ?>', {"uid":$(this).attr('data-id'),"suggest":0}, function(data) {
									  _obj.html('<em>未推荐</em>'); 
                                     
	                             })
							}							
							});
					})
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
