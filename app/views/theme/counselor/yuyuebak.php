<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">预约管理</a>(点击消费金额编辑,按回车键(Enter)保存;确认预约需要5积分,您当前有<?php echo $jifen ?>积分)</li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="yuyue_form">
                            	<ul>
                                	<li class="Vertical01">时间</li>
                                    <li class="Vertical02">科室</li>
                                    <li class="Vertical03">姓名</li>
                                    <li class="Vertical04">手机</li>
                                    <li class="Vertical05">性别</li>
                                    <li class="Vertical06">年龄</li>
                                    <li class="Vertical07">确认预约</li>
                                    <li class="Vertical08">备注</li>
                                    <li class="Vertical09">消费金额(￥)</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                     
                              
                                <?php
								foreach($data as $row)
								{
									echo '<ul data-id="'.$row['id'].'">
                                	<li class="Vertical01">'.$row['yuyueDate'].'</li>
                                    <li class="Vertical02">'.$this->yisheng->search($row['keshi']).'</li>
                                    <li class="Vertical03">'.$row['name'].'</li>
                                    <li class="Vertical04">'.substr($row['phone'],0,5).'***</li>
                                    <li class="Vertical05">'.$row['sex'].'</li>
                                    <li class="Vertical06">'.$row['age'].'</li>
                                    <li class="Vertical07">'.($row['state']==0?'<a title="点击完成已审核状态" class="yuyue">待审核</a>':($row['state']==1?'<a title="点击修改预约状态" class="yuyue">是</a>':'<a title="点击修改预约状态" class="unyuyue">否</a>')).'</li>
                                    <li class="Vertical08">'.($row['comment']==''?'<a href="'.site_url('counselor/beizhu').'?height=120&width=320&dataid='.$row['id'].'" title="添加备注" class="thickbox">备注</a>':$row['comment']).'</li>
                                    <li class="Vertical09 editamount" data-id="'.$row['id'].'">'.$row['amout'].'</li>
                                   
                                </ul> <div class="clear" style="clear:both;"></div>';
									
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
                    </div> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> <script type="text/javascript">$(function(){
    
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/yueyueset', {"dataid":dataid,"amount":amount}, function(data) {  

   });}</script>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 