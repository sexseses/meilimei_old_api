<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a  >后台管理系统</a></li><li style="float:right;"></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue"  style="height:500px">
                        	<div>今天是 <?php echo date('Y-m-d',time()) ?></div>
                        	<div>&nbsp;</div>
                        	<div>短信余额：<?php echo $getBalance ?>元 </div>
                        </div>
                    </div>  
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
