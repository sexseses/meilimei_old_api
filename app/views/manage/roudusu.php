<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 

<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><form id="userform" accept-charset="utf-8" method="get" action="<?php echo site_url('manage/roudusu') ?>"> 
                            	<ul>
                                    <li>下单时间<input style="width:20px;" type="checkbox" name="opendate" <?php echo $this->input->get('opendate')?'checked="checked"':'' ?> value="1" />
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                                    <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li>
                                    <li>手机号码<input name="phone" type="text" value="<?php echo $this->input->get('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form><?php echo isset($total_rows)?$total_rows:'' ?>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:5%">ID</li> 
                                    <li style="width:5%">名字</li> 
                                    <li style="width:10%">手机</li>
                                    <li style="width:20%">机构</li>
                                    <li style="width:15%">下单时间</li>
                                    <li style="width:8%">支付状态</li>
                                    <li style="width:10%">验证码</li>
                                    <li style="width:15%">支付宝订单号</li>                                  
                                    <li class="width:5%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php if(isset($results)){
                                      foreach($results as $row){
                                        if($row->pay_state=='0'||empty($row->pay_state)){
                                        	$pay_state = '未支付';
                                        }elseif($row->pay_state=='1'){
                                        	$pay_state = '已支付';
                                        }
                                        if(empty($row->trade_no)){
                                        	$trade_no = '';
                                        }else{
                                        	$trade_no = $row->trade_no;
                                        }
                                          echo ' <ul class="">
                                	 <li style="width:5%">'.$row->id .'</li>
                                     <li style="width:5%">'.$row->name .'</li>
                                     <li style="width:10%">'.$row->mobile .'</li>
                                     <li style="width:20%">'.$row->mechanism .'</li>
                        	         <li style="width:15%">'.(empty($row->time)?'无':date('Y-m-d H:i:s',$row->time)).'</li>
								     <li style="width:8%">'.$pay_state.'</li>
                                     <li style="width:10%">'.$row->capture.'</li>
                                     <li style="width:15%">'.$trade_no.'</li>
									 <li style="width:5%"><a onclick="return sendCode('.$row->id.')">验证码</a><li/>
                                     <div class="clear" style="clear:both;"></div></ul>';
                                      }
                                  }
                                  
								 ?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                   <?php echo isset($pagelink)?$pagelink:''?>
                                </div>
                            </div>
                        </div>
  </div>
  <div class="clear" style="clear:both;"></div>
</div>
<script>
	
function sendCode(id) {
	var param = new function() {
		this.id = id;
	};
	if(confirm("确认发送验证码?")){
		$.ajax({
			type : 'post',
			url:'<?php echo site_url('manage/roudusu/send')?>',
			data : param,
			async:false,
			timeout : 60000,
			dataType : "json",
			success : function(result) {
				if(result.code=='0'){
				    alert(result.msg);
				}else if(result.code=='1'){
					alert('发送成功');
					window.location.href="<?php echo site_url('manage/roudusu')?>";
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