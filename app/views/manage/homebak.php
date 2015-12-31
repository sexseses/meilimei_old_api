<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> 
<div class="page_content937">
  <div class="institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">客户记录</a>客户记录</a>(点击消费金额编辑,按回车键(Enter)保存)</li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
                            	<ul><li style="width:10%">名称</li>
                                	<li style="width:10%">时间</li>
                                    <li style="width:10%">科室</li>
                                    <li style="width:10%">姓名</li>
                                    <li style="width:10%">手机</li>
                                    <li style="width:7%">性别</li> 
                                    <li style="width:10%">确认预约</li>
                                    <li style="width:8%">备注</li>
                                    <li style="width:12%">金额￥</li>
                                    <li style="width:7%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                 <?php
								foreach($data as $row)
								{   switch($row['role_id']){
									  case 1:
									     $link = site_url('manage/users/detail/'.$row['userto']);
									   break;
									   case 2:
									     $link = site_url('manage/yishi/detail/'.$row['userto']);
									   break;
									   default  :
									     $link = site_url('manage/yiyuan/detail/'.$row['userto']); 
								   } 
								   $wlink = $row['userby']!=0?'<a href="'.site_url('manage/users/detail/'.$row['userby']).'">'.$row['name'].'<a>':$row['name'];
									echo '<ul data-id="'.$row['id'].'"><li style="width:10%"><a target="_blank" href="'.$link.'">'.$row['alias'].'</a></li>
                                	<li style="width:10%">'.$row['yuyueDate'].'</li>
                                    <li style="width:10%">'.$this->yisheng->search($row['keshi']).'</li>
                                    <li style="width:10%">'.$wlink.'</li>
                                    <li style="width:10%">'.$row['phone'].'</li>
                                    <li style="width:7%">'.$row['sex'].'</li> 
                                    <li style="width:8%">'.($row['state']==1?'<a title="点击修改预约状态" class="yuyue">是</a>':'<a title="点击修改预约状态" class="unyuyue">否</a>').'</li>
                                    <li style="width:10%">'.($row['comment']==''?'<a href="'.site_url('counselor/beizhu').'?height=120&width=320&dataid='.$row['id'].'" title="添加备注" class="thickbox">备注</a>':$row['comment']).'</li>
                                    <li style="width:12%" class=" editamount" data-id="'.$row['id'].'">'.$row['amout'].'</li> <li style="width:7%"><a href="'.site_url('manage/home/del/'.$row['id']).'">删除</a></li>
                                  
                                </ul>  <div class="clear" style="clear:both;"></div>';
									
								}
								
								?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right">
                            	<ul>
                                    <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                                    <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                </ul>
                                <h5>第<?php echo $offset ?>-<?php echo $offset+count($data)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                            </div>
                            </div>
                        </div>
                    </div><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script><script type="text/javascript">$(function(){
    
    var numTd = $(".editamount"); 
    numTd.click(function() {     
        var tdObj = $(this);
        if (tdObj.children("input").length > 0) { 
            return false;
        }
        var text = tdObj.html();  
        tdObj.html(""); 
        var inputObj = $("<input type='text'>").css("border-width","0")
            .css("font-size","16px").width(tdObj.width())
            .css("background-color",tdObj.css("background-color"))
            .val(text).appendTo(tdObj); 
        inputObj.trigger("focus").trigger("select");
        inputObj.click(function() {
            return false;
        }); 
        inputObj.keyup(function(event){ 
            var keycode = event.which; 
            if (keycode == 13  ) { 
                var inputtext = $(this).val(); 
                tdObj.html(inputtext);
				sendamount(inputtext,tdObj.attr("data-id"));
            } 
            if (keycode == 27) { 
                tdObj.html(text);
				sendamount(inputtext,tdObj.attr("data-id"));
            }
        });
    });
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/myueyueset', {"dataid":dataid,"amount":amount}, function(data) {  
   });}</script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
