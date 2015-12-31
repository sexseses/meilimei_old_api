<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加目录</a></li>
                                <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                                <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                            </ul>
                        </div>
                        <div class="manage_search"><form method="get" action="<?php echo base_url('manage/diary/category'); ?>">
                                <ul>
                                    <li>关键词<input name="sname" type="text" value="<?php echo $this->input->get('sname') ?>"></li>
                                    <li>用户名<input type="text" name="username" value="<?php echo $this->input->get('username');?>"/></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>

                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:8%">编号</li>
                                    <li style="width:18%">目录标题</li>
                                    <li style="width:18%">背景图</li>
                                    <li style="width:18%">发布人</li>
                                    <li style="width:7%">状态</li>
                                    <li class="width:25%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php

								foreach($results as $row){
                                    $is = $row->is?'显示':'隐藏';
								  echo ' <ul class="">
                                	 <li style="width:8%">'.$row->ncid .'</li>
                                     <li style="width:18%">'.$row->title.'</li>
                                    <li style="width:18%"><img width="150px" height="200px" src="http://pic.meilimei.com.cn/upload/'.$row->imgurl .'"/></li>
                                    <li style="width:18%">'.$row->username.'</li>
                                    <li style="width:7%">'.$is.'</li>
								    <li class="width:15%"><a href="'.site_url('manage/diary/addcategory/'.$row->ncid).'?type=2&ncid='.$row->ncid.'">编辑</a>&nbsp;&nbsp;<a onclick="return confirm(\'确认删除 '.$row->ncid.'?\')" href="'.site_url('manage/diary/delcategory/'.$row->ncid).'">删除</a></li> <div class="clear" style="clear:both;"></div>
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
