<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                        <div class="manage_yuyue" >
                        	<div class="manage_search"> 
                            <?php echo isset($total_rows)?$total_rows:'' ?>
                          </div>
                        	<div class="manage_yuyue_form">
                            	<ul> <li style="width:5%">ID</li> 
                                    <li style="width:5%">名字</li> 
                                    <li style="width:10%">手机</li>
                                    <li style="width:10%">机构</li>
                                    <li style="width:15%">下单时间</li>
                                    <li style="width:8%">支付状态</li>
                                    <li style="width:10%">验证码</li>
                                    <li style="width:25%">订单号</li>                                  
                                    <li class="width:5%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php  
                                      foreach($coll_rs as $row){
                                        if($row->pay_state=='0'||empty($row->pay_state)){
                                        	$pay_state = '未支付';
                                        }elseif($row->pay_state=='1'){
                                        	$pay_state = '支付宝支付';
                                        }elseif($row->pay_state=='2'){
                                            $pay_state = '微信支付';
                                        }
                                        if(empty($row->order_no)){
                                        	$order_no = '';
                                        }else{
                                        	$order_no = $row->order_no;
                                        }
                                        echo ' <ul class="">
                                    	   <li style="width:5%">'.$row->id .'</li>
                                         <li style="width:5%">'.$row->name .'</li>
                                         <li style="width:10%">'.$row->mobile .'</li>
                                         <li style="width:10%">'.$row->mechanism .'</li>
                            	           <li style="width:15%">'.(empty($row->time)?'无':date('Y-m-d H:i:s',$row->time)).'</li>
    								                     <li style="width:8%">'.$pay_state.'</li>
                                         <li style="width:10%">'.$row->capture.'</li>
                                         <li style="width:25%">'.$order_no.'</li>
    									                   <li style="width:5%"><a onclick="return sendCode('.$row->id.')">验证码</a><li/>
                                         <div class="clear" style="clear:both;"></div></ul>';
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
			url: 'http://www.meilimei.com/event/index?act=send',
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