<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/questions')?>">咨询管理</a></li><li><a href="<?php echo site_url('manage/questions/newest')?>">最新信息</a></li>     </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form method="get" action="<?php echo site_url('manage/questions') ?>">
                            	<ul> 
                                   <li>标题<input name="sname" type="text" value="<?php echo $this->input->get('sname') ?>"></li> 
                                   <li>用户名/手机<input name="uname" type="text" value="<?php echo $this->input->get('uname') ?>"></li> 
                                   <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                                   <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li>
                                   <li>过滤重复<input style="width:20px;" type="checkbox" name="filts" <?php  echo $filts?'checked="checked"':''; ?> value="1" /></li>
                                    <li><input  name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form><input name="deleteid" id="deleteid" type="button" value="删除" class="search">
                                <?php echo '总数:'.$total_rows ?>
                                 
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul><li style="width:10%"><a id="selectall">[全部]</a><a id="selectnone">[取消]</a> 用户名</li>
                                    <li style="width:15%">邮箱/手机</li>
                                	<li style="width:20%">标题</li>
                                    <li style="width:10%">咨询时间</li>
                                    <li style="width:12%">回答医生</li>
                                    <li style="width:5%">状态</li>
                                    <li style="width:5%">系统</li>
                                    <li style="width:5%">位置</li>
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){ 
 									echo '<ul style="width: 100%;float:left;'.($row->acstate==0?'background:#efefee':'').'"><li style="width:10%"><input type="checkbox" value="'.$row->id.
									'" name="seclc[]" /> '.$row->alias.'</li>
 									<li style="width:15%"><a style="width:15%" href="'.base_url('manage/users/detail/'.$row->uid).'"><div style="width:100%;word-break:break-all;">'.($row->phone==''?$row->email:$row->phone).'</div></a></li>
                                	<li style="width:20%">'.$row->title.'</li>
                                  	<li style="width:10%">'.date('Y-m-d H:i',$row->cdate).'</li>
                                  	<li style="width:12%">'.$row->doctors.'</li><li style="width:5%">'.($row->has_complete==flase?'未完成':'完成').'</li>
									<li style="width:5%">'.$row->device.'</li><li style="width:5%">'.($row->city=='0'?'无':$row->city).'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->title.'?\')" href="'.base_url('manage/questions/del/'.$row->id).'" >删除</a>  <a href="'.base_url('manage/questions/detail/'.$row->id).'" >详细</a>'.($row->acstate==0?' <a data-id="'.$row->id.'" class="setcontact"  >已联系</a>':'').'</li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right"  >
                                   <?php echo $pagelink ?>
                                </div>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript">
 $(function(){
  $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();  }) 
    $("#selectall").click(function(){ 
		$(".manage_yuyue_form :checkbox").attr("checked",true); 
    });
	$("#selectnone").click(function(){ 
		$(".manage_yuyue_form :checkbox").attr("checked",false); 
    }); 
	$("#deleteid").click(function(){
		$(".manage_yuyue_form :checkbox:checked").each(function(){
			 $(this).parent().parent().hide(300);
           $.get("../manage/questions/del/"+$(this).val(), {id: $(this).val()},     
           function (data, textStatus){     
           }, "json");
        })
	})
   $(".setcontact").click(function(){
		 $.get("../manage/questions/contact/"+$(this).attr('data-id'), {id: $(this).attr('data-id')},     
           function (data, textStatus){     
           }, "json");$(this).parent().parent().css("background","none"); 
		   $(this).remove(); 
	})   
 
   </script>
  </div>
</div>
