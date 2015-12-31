<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">.manage_search ul li input{width:auto;}</style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">日志</a></li> 
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/logtrack') ?>"> 
                            	<ul> 
                                    <li>用户UID<input name="uid" type="text" value="<?php echo $this->input->get('uid') ?>" maxlength="62"></li> 
                                    <li>手机/Email<input name="email" type="text" value="<?php echo $this->input->get('email') ?>"></li> 
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:15%">手机/Email</li> 
                                    <li style="width:15%">名字</li>
                                    <li style="width:10%">ip</li>
                                    <li style="width:20%">位置</li>
                                    <li style="width:10%">系统</li>
                                    <li style="width:8%">浏览器</li>
                                    <li style="width:13%">时间</li> 
                                    <li style="width:5%">状态</li> 
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								function track(&$db,$uid){
        $yd = $db->query("SELECT distinct(addr) FROM user_login  where user_login.uid = $uid ORDER BY user_login.id DESC  LIMIT 30")->result_array();
       
	    $passer = $db->query("SELECT id FROM user_login  where user_login.uid = $uid and pass !='' ORDER BY user_login.id DESC  LIMIT 30")->result_array();
        if(count($yd)>1){
          if(count($passer)>3){
          	 return '危险';
          }else{
             return '异常';
          }
        }else{
		  if(count($passer)>3){ 
             return '非法';
          }
		}
        return '正常';
    }
								foreach($results as $row){
								  if($row->uid){
									  switch($row->rid){
										case 1:
										   $link = 'http://www.meilimei.com/manage/users/detail/'.$row->uid.'.html'; 
										  break;  
										case 1: 
										  $link = 'http://www.meilimei.com/manage/yishi/detail/'.$row->uid.'.html';
										  break;
										default:
										   $link = 'http://www.meilimei.com/manage/yiyuan/detail/'.$row->uid.'.html';
										  break;
									  }
									  echo ' <ul>
                                	 <li style="width:15%"><a href="'.$link.'">'.$row->name .'</a></li>  
                                     <li style="width:15%">'.$row->alias .'</li> 
									 <li style="width:10%">'.$row->ip .'</li> 
									 <li style="width:20%">'.$row->addr .'</li>  
									 <li style="width:10%">'.$row->system .'</li> 
									 <li style="width:8%">'.$row->browser .'</li> 
									 <li style="width:13%">'.date('Y-m-d H:i:s',$row->cdate) .'</li> 
									 <li style="width:5%">'.track($this->db,$row->uid).'</li> 
									 <div class="clear" style="clear:both;"></div>
                                </ul>';	
								  }else{ 
									  echo ' <ul style="background:#FDD">
                                	 <li style="width:15%"><a href="'.site_url('manage/logtrack?email='.$row->name.'&submit=1').'">'.$row->name .'</a></li>  
                                     <li style="width:15%"></li> 
									 <li style="width:10%">'.$row->ip .'</li> 
									 <li style="width:20%">'.$row->addr .'</li>  
									 <li style="width:10%">'.$row->system .'</li> 
									 <li style="width:8%">'.$row->browser .'</li> 
									 <li style="width:13%">'.date('Y-m-d H:i:s',$row->cdate) .'</li> 
									 <div class="clear" style="clear:both;"></div>
                                </ul>';	
								  } 
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
