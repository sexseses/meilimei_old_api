<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="<?php echo site_url('manage') ?>">客户记录</a> 添加预约</li><li style="float:right;"></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
								<div class="manage_search">
								<form method="post">
                            	<ul>  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                               <Li style="display:block;width:100%;height:50px;"><label>备注</label><textarea style="height:50px;width:500px" name="remarks"></textarea></Li>
                               <li><label>金额</label><input value="未定" name="amout"></li>
                               <li><label>姓名</label><input value="<?php echo $user[0]->alias ?>" name="name"></li>
                               <li><label>年龄</label><input value="20" name="age"></li>
                               <li><label>地区</label><input value="<?php echo $user[0]->city ?>" name="city"></li>
                               <li style="display:block;width:100%;height:25px;"><label>预约时间</label><input class="datepicker" value="<?php echo date('Y-m-d')?>" name="yuyueDate"></li>
                               <li style="display:block;width:100%;height:25px;"><label>时间附加信息</label><input value="10:00-12:00" name="extraDay">如早上10-12点</li>
                               <li><label>手机</label><input value="<?php echo $user[0]->phone ?>" name="phone"></li>
                                   <li><input name="paidan" type="submit" value="添加" style="background:#008000;color:#fff;" class="paidan"></li>
                                </ul>
                                </form>
                            	</div>
                            	 
                                 <?php  
                                function DS($res,$city){
									$html = '';
									foreach($res as $r){
                                       $r['city']==$city&&$html .='<option value="'.$r['userid'].'">'.$r['name'].'</option>';
                                    }
									return $html;
								} ?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> 
   <script type="text/javascript"> 
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });        	         
 
</script>
   
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
