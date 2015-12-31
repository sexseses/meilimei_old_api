<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                                <li><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">记目录</a></li>
                                <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                                <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                            </ul>
                        </div>
                        <div class="manage_search"><form method="get" action="<?php echo base_url('manage/diary'); ?>">
                                <ul>
                                    <li>关键词<input name="sname" type="text" value="<?php echo $this->input->get('sname') ?>"></li>
                                    <li>用户id<input name="userid" type="text" value="<?php echo $this->input->get('userid') ?>"></li>
                                    <li>类型<select name="types"><option value="">全部</option><option <?php echo $this->input->get('types')=='1'?'selected="selected"':'' ?> value="1">windows</option><option <?php echo $this->input->get('types')=='0'?'selected="selected"':'' ?> value="0">非 windows</option></select></li>
                                    <li>标签<input type="text" name="tags" value="<?php echo $this->input->get('tags');?>"/></li>
                                    <li>用户名<input type="text" name="username" value="<?php echo $this->input->get('username');?>"/></li>
                                    <li>启动<select name="loading"><option value="">全部</option><option <?php echo $this->input->get('loading')=='1'?'selected="selected"':'' ?>  value="1">是</option><option <?php echo $this->input->get('loading')==='0'?'selected="selected"':'' ?> value="0">否</option></select></li>
                                    <li>推荐<select name="is_front"><option value="">全部</option><option <?php echo $this->input->get('is_front')=='1'?'selected="selected"':'' ?>  value="1">是</option><option <?php echo $this->input->get('is_front')==='0'?'selected="selected"':'' ?> value="0">否</option></select></li>
                                    <li>开始时间:<input type="text" value="" name="stime" id="stime" class="datepicker" /> 结束时间:<input type="text" value="" name="etime" id="etime" class="datepicker" /></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                    <li><a href="<?php echo base_url('manage/diary/pageview'); ?>">添加浏览量</a></a></li>
                                </ul></form>

                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:6%">编号</li>
                                    <li style="width:10%">用户名</li>
                                    <li style="width:18%">美人记内容</li>
                                    <li style="width:18%">美人记图片</li>
                                    <li style="width:7%">美人记项目</li>
                                    <li style="width:6%">浏览量</li>
                                    <li style="width:10%">来源</li>
                                    <li style="width:4%">评论回复数</li>
                                    <li class="width:25%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php

								foreach($results as $row){

								  echo ' <ul class="">
                                	 <li style="width:6%">'.$row->nid .'</li>
                                	 <li style="width:10%">'.$row->username .'</li>
                                     <li style="width:18%">'.$row->content.'</li>
                                    <li style="width:18%"><img width="150px" height="200px" src="'.$row->imgurl .'"/></li>
                                    <li style="width:7%">'.$row->item_name .'</li>
                                    <li style="width:6%">'.intval($row->pageview) .'</li>
                                    <li style="width:10%">'.($row->os?'windows':'非windows').'</li>
                                    <li style="width:4%">'.$row->total_comments.'</li>
								    <li class="width:15%"><a href="'.site_url('manage/diary/detail/'.$row->nid).'">查看</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href="'.site_url('manage/diary/add/'.$row->nid).'?type=1&nid='.$row->nid.'">编辑</a>&nbsp;&nbsp;<a onclick="return confirm(\'确认删除 '.$row->nid.'?\')" href="'.site_url('manage/diary/del/'.$row->nid).'">删除</a></li> <div class="clear" style="clear:both;"></div>
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
