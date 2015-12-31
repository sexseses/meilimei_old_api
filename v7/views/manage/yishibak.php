<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">医师管理</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/yishi'); ?>
                            	<ul> 
                                    <li>医师姓名<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>" maxlength="62"></li> 
                                    <li>城市<input name="city" type="text" value="<?php echo $this->input->post('city') ?>"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->post('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div> 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical17">医生姓名</li> 
                                    <li class="Vertical19">手机</li>
                                    <li class="Vertical20">Email</li>
                                    <li class="Vertical21">城市</li>
                                    <li class="Vertical22">电话</li>
                                    <li class="Vertical23">详细</li>
                                    <li class="Vertical24">审核</li> <li class="Vertical18">回答</li><li class="Vertical24">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                             <?php 
								foreach($results as $row){
								  echo ' <ul>
                                	<li class="Vertical17">'.$row->alias .'</li>
                                    <li class="Vertical19">'.$row->phone .'</li> <li class="Vertical20">'.$row->email .'</li>
                                    <li class="Vertical21">'.$row->city .'</li>  <li class="Vertical22">'.$row->tel .'</li> 
									<li class="Vertical23"><a href="'.site_url('manage/yishi/detail/'.$row->id).'">查看</a></li> 
								    <li class="Vertical24" data-id="'.$row->id .'">'.($row->banned?'禁用':'<a>正常</a>').'</li><li class="Vertical18"><a href="'.site_url('manage/yishi/answers/'.$row->id).'">查看</a></li> <li><a onclick="return confirm(\'确认删除 '.$row->alias.'?\')" href="'.site_url('manage/yishi/del/'.$row->id).'">删除</a> <a data-id="'.$row->id .'" class="suggest ">'.($row->suggested?'已推荐':'<em>未推荐</em>').'</a></li>
									
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
								 $.get('http://www.meilimei.com/manage/yishi/userac', {"uid":$(this).attr('data-id'),"banned":1}, function(data) {
									  _obj.text('禁用'); 
	                             })
							}else if(_obj.text()=='禁用'){ _obj.text('处理中');  
								$.get('http://www.meilimei.com/manage/yishi/userac', {"uid":$(this).attr('data-id'),"banned":0}, function(data) {
									  _obj.html('<a>正常</a>'); 
                                     
	                             })
							}							
							});
							$(".suggest").click(function(){ 
						    var _obj = $(this);
							if(_obj.text()=='未推荐'){  _obj.text('处理中'); 
								 $.get('http://www.meilimei.com/manage/yishi/suggest', {"uid":$(this).attr('data-id'),"suggest":1}, function(data) {
									  _obj.text('已推荐'); 
	                             })
							}else if(_obj.text()=='已推荐'){ _obj.text('处理中');  
								$.get('http://www.meilimei.com/manage/yishi/suggest', {"uid":$(this).attr('data-id'),"suggest":0}, function(data) {
									  _obj.html('<em>未推荐</em>'); 
                                     
	                             })
							}							
							});
					})
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
