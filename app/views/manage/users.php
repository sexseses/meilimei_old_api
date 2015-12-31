<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/users'); ?>">普通用户管理</a></li><li><a href="<?php echo site_url('manage/users/daren'); ?>">达人管理</a></li> <li><a href="<?php echo site_url('manage/users/today'); ?>">今日联系</a></li> <li><a href="<?php echo site_url('manage/users/sendsms'); ?>">短信通知</a></li><li><a href="<?php echo site_url('manage/users/total'); ?>">统计</a></li>
                                <li><a href="<?php echo site_url('manage/users/export'); ?>">导出</a></li> 
                                </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/users') ?>"> 
                            	<ul> 
                                    <li>邮箱<input name="email" type="text" value="<?php echo $this->input->get('email') ?>" maxlength="62"></li>  
                                    <li>按时间过滤<input style="width:20px;" type="checkbox" name="opendate" <?php echo $this->input->get('opendate')?'checked="checked"':'' ?> value="1" />
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                                    <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->get('phone') ?>"></li>
                                    <li>未联系<input  <?php if($this->input->get('noc')) { echo 'checked="checked"'; } ?>  style="width:20px" name="noc" type="checkbox" /></li>
                                    <li>用户名<input type="text" name="username" value="<?php echo $this->input->get('username');?>"/></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form><?php echo $total_rows ?>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:8%">ID</li> 
                                    <li style="width:8%">名字</li> 
                                    <li style="width:10%">手机</li>
                                    <!--li style="width:10%">Email</li-->
                                    <li style="width:15%">城市</li>
                                    <li style="width:10%">时间</li>
                                    <li style="width:10%">来源</li>
                                    <li class="Vertical24">审核</li>
                                    <li class="width:15%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php
                                  
								foreach($results as $row){
								  echo ' <ul class="'.($row->states==1?'visiteid':'').'">
                                	 <li style="width:8%">'.$row->id .'</li> <li style="width:8%">'.$row->alias .'</li> 
                                    <li style="width:10%">'.$row->phone .'</li>
                                    <!--li style="width:15%">'.$row->email .'</li-->  <li style="width:10%">'.$row->city .'</li>  <li style="width:10%">'.date('Y-m-d',$row->created) .'</li>
									<li style="width:10%">'.($row->regfrom==0?'未知':($row->regfrom==1?'网络':'手机')).$row->regsys.'</li> 
								    <li class="Vertical24" data-id="'.$row->id .'">'.($row->banned?'禁用':'<a>正常</a>').'</li><li class="Vertical25" daren="'.$row->id .'">'.(($row->daren==0)?'<a>申请达人</a>':'<a>未审核</a>').'</li><li class="width:15%"><a onclick="return confirm(\'确认删除 '.$row->alias.'?\')" href="'.site_url('manage/users/del/'.$row->id).'">删除</a> <a href="'.site_url('manage/users/allquestion/'.$row->id).'">咨询('.$row->reNums.')</a>   <a href="'.site_url('manage/home/addyuyue/'.$row->id).'">添加预约</a> <a href="'.site_url('manage/users/detail/'.$row->id).'">查看</a> <a href="'.site_url('manage/users/track/'.$row->id).'">跟踪</a></li> <div class="clear" style="clear:both;"></div>
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

                        $(".Vertical25").click(function(){
                            var _obj = $(this);

                            if(_obj.text()=='申请达人'){  _obj.text('处理中');
                                $.get('<?php echo site_url('manage/users/userac'); ?>', {"uid":$(this).attr('daren'),"daren":2}, function(data) {
                                    _obj.text('未审核');
                                })
                            }else if(_obj.text()=='未审核'){ _obj.text('处理中');
                                $.get('<?php echo site_url('manage/users/userac'); ?>', {"uid":$(this).attr('daren'),"daren":0}, function(data) {
                                    _obj.html('<a>申请达人</a>');

                                })
                            }
                        });
					 
					})
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
