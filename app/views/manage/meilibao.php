<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 

<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/meilibao') ?>"> 
                            	<ul> 
                                   <li>人员
                                    <select name="ome"> 
                                    <option value="0">全部</option>
                                    <?php
									foreach($managers as $r){
										echo '<option '.($this->input->get('ome') ==$r['id']?'selected="selected"':'').' value="'.$r['id'].'">'.$r['alias'].'</option>';
									}
									?>
                                    <li>投保时间<input style="width:20px;" type="checkbox" name="opendate" <?php echo $this->input->get('opendate')?'checked="checked"':'' ?> value="1" />
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                                    <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->get('phone') ?>"></li>
                                    </select> </li>
                                    <li>投保状态
                                    <select name="tbzt_status">
                                    <option value="" <?php echo ($this->input->get('tbzt_status') ===''?'selected="selected"':'') ?>>全部</option>
                                    <option value="0" <?php echo ($this->input->get('tbzt_status') ==='0'?'selected="selected"':'') ?>>-</option>
                                    <option value="1" <?php echo ($this->input->get('tbzt_status') =='1'?'selected="selected"':'') ?>>未投保</option>
                                    <option value="2" <?php echo ($this->input->get('tbzt_status') =='2'?'selected="selected"':'') ?>>已投保</option>
                                   </select> </li>
                                    <li>未联系<input  <?php if($this->input->get('noc')) { echo 'checked="checked"'; } ?>  style="width:20px" name="noc" type="checkbox" /></li> 
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form><?php echo $total_rows ?>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:10%">ID</li> 
                                    <li style="width:10%">名字</li> 
                                    <li style="width:10%">手机</li>
                                    <li style="width:10%">城市</li>
                                    <li style="width:15%">投保时间</li>
                                    <li style="width:10%">来源</li>
                                    <li style="width:10%">投保状态</li>                                    
                                    <li class="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php 
								foreach($results as $row){
                                  if(empty($row->tbzt_status)||$row->tbzt_status=='0'){
                                      $tbzt_status ='-';
                                  }elseif($row->tbzt_status=='1'){
                                  	  $tbzt_status ='未投保';
                                  }elseif($row->tbzt_status=='2'){
                                  	  $tbzt_status ='已投保';
                                  }
								  echo ' <ul class="'.($row->states==1?'visiteid':'').'">
                                	 <li style="width:10%">'.$row->id .'</li> 
                                     <li style="width:10%">'.$row->alias .'</li> 
                                     <li style="width:10%">'.$row->phone .'</li>
                                     <li style="width:10%">'.$row->city .'</li> 
                        	         <li style="width:15%">'.(empty($row->tb_time)?'无':date('Y-m-d H:i:s',$row->tb_time)).'</li>
									 <li style="width:10%">'.($row->regfrom==0?'未知':($row->regfrom==1?'网络':'手机')).$row->regsys.'</li> 
								     <li style="width:10%">'.$tbzt_status.'</li>
									 <li><a href="'.site_url('manage/meilibao/detail/'.$row->id).'">查看</a>
                                            <a onclick="return sendCode(1,'.$row->id.')">验证码1</a>
                                            <a onclick="return sendCode(2,'.$row->id.')">验证码2</a>
                                     <li/><div class="clear" style="clear:both;"></div>
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
  </div>
  <div class="clear" style="clear:both;"></div>
</div>
<script>
$(document).ready(function(){
	$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();});
	
function sendCode(code,id) {
	var mone;
	var param = new function() {
		this.code = code;
		this.id = id;
	};
	if(code=='1'){
	    money = '(40元)';
	}else{
		money = '(80元)';
	}
	if(confirm("确认发送验证码"+money+"?")){
		$.ajax({
			type : 'post',
			url:'<?php echo site_url('manage/meilibao/send')?>',
			data : param,
			async:false,
			timeout : 60000,
			dataType : "json",
			success : function(result) {
				if(result.code=='0'){
				    alert(result.msg);
				}else if(result.code=='1'){
					alert('发送成功');
				}
			},
			error : function(result, status, errorThrown) {
				alert(result);
				execCommonAjaxError(result, status, errorThrown);
			}
		});
	}else{
	    return false;
	}
}
 
			 
</script>