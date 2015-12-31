<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加美人记</a></li>
                                <li><a href="<?php echo site_url('manage/diary/category'); ?>">美人计目录管理</a></li>
                                <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加美人记目录</a></li>
                                <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/diary/check'); ?>">待审核美人记</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:8%">编号</li>
                                    <li style="width:18%">美人计内容</li>
                                    <li style="width:18%">美人计图片</li>
                                    <li style="width:7%">美人计项目</li>
                                    <li style="width:8%">美人计医生</li>
                                    <li style="width:10%">美人计医院</li>
                                    <li style="width:10%">评论回复数</li>
                                    <li class="width:25%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php

								foreach($results as $row){

								  echo ' <ul class="">
                                	 <li style="width:8%">'.$row->nid .'</li>
                                     <li style="width:18%">'.$row->content.'</li>
                                    <li style="width:18%"><img width="150px" height="200px" src="http://pic.meilimei.com.cn/upload/'.$row->imgurl .'"/></li>
                                    <li style="width:7%">'.$row->item_name .'</li>
                                    <li style="width:8%">'.$row->doctor .'</li>
                                    <li style="width:10%">'.$row->hospital.'</li>
                                    <li style="width:10%">'.$row->total_comments.'</li>
								    <li class="width:15%">'.($row->banned==1?'已禁用':' <a  class="forbiduser" data-id="'.$row->uid .'" title="禁用改用户">禁用</a>').'<a href="'.site_url('manage/diary/review/'.$row->nid).'">通过</a>&nbsp;&nbsp;&nbsp;&nbsp;<a  href="'.site_url('manage/diary/add/'.$row->nid).'?type=1&nid='.$row->nid.'">编辑</a>&nbsp;&nbsp;<a onclick="return confirm(\'确认删除 '.$row->nid.'?\')" href="'.site_url('manage/diary/del/'.$row->nid).'">删除</a></li> <div class="clear" style="clear:both;"></div>
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
                    $(".forbiduser").click(function () {
                        var curobj = $(this);
                        curobj.text('处理中');
                        $.get('http://www.meilimei.com/manage/topic/userbanned/', {
                            "uid": curobj.attr('data-id'),
                            "banned": 1
                        }, function (data) {
                            curobj.text('已禁用');
                        })
                    });
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
