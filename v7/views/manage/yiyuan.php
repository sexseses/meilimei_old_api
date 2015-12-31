<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">.manage_search ul li input{width:auto;}</style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">机构</a></li><li><a href="<?php echo site_url('manage/yiyuan/add') ?>">添加机构</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/yiyuan') ?>"> 
                            	<ul> 
                                    <li>医院名称<input name="sname" type="text" value="<?php echo $this->input->get('sname') ?>" maxlength="62"></li> 
                                    <li>城市<input name="city" type="text" value="<?php echo $this->input->get('city') ?>"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->get('phone') ?>"></li>
                                    <li><input name="state" type="checkbox" <?php echo $this->input->get('state')?'checked="checked"':'' ?> value="1">推荐</li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                    <li><?php echo $total_rows; ?></li>
                                </ul></form>
                            </div> 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:200px">医院名称</li>
                                    <li class="Vertical18">联系人</li>
                                    <li class="Vertical19">电话</li>
                                    <li class="Vertical19">手机</li>
                                    <li class="Vertical20">Email</li>
                                    <li class="Vertical21">城市</li>
                                    <li class="Vertical23">详细</li>
                                    <li class="Vertical21">是否合作</li>
                                    <li class="Vertical24">审核</li>
                                    <li class="Vertical24">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($results as $row){
                                    $str = "";
                                    if($row->team == 1){
                                        $str = "[团]";
                                    }
 
								  echo ' <ul>
                                	<li style="width:200px">'.$str.strip_tags($row->name) .'</li><li class="Vertical18">'.$row->contactN .'</li> 
                                    <li class="Vertical19">'.$row->tel .'</li> <li class="Vertical20">'.$row->phone .'</li>
                                    <li class="Vertical21">'.$row->email .'</li>  <li class="Vertical22">'.$row->city .'</li> 
									<li class="Vertical23"><a href="'.site_url('manage/yiyuan/detail/'.$row->id).'">查看</a></li> 
									<li class="Vertical21 hezuo" data-id="'.$row->id .'">'.($row->state?'是':'<a>否</a>').'</li>
								    <li class="Vertical24 ustates" data-id="'.$row->id .'">'.($row->banned?'禁用':'<a>正常</a>').'</li><li ><a onclick="return confirm(\'确认删除 '.$row->name.'?\')" href="'.site_url('manage/yiyuan/del/'.$row->id).'">删除</a>   <a href="'.site_url('manage/yiyuan/track/'.$row->id).'">派单记录</a>
									   <a href="'.site_url('manage/yiyuan/comment/'.$row->id).'">评论</a>  
									</li> <div class="clear" style="clear:both;"></div>
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
