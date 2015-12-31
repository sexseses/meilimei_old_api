<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="#">医师管理</a> </li><li><a href="<?php echo site_url('counselor/similaryishi/') ?>">可能所属医师</a></li> <li><a href="<?php echo site_url('counselor/addyishi/') ?>">添加医师</a></li> 
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="yuyue_form">
                            	<ul>  
                                    <li style="width:10%">姓名</li>
                                    <li style="width:20%">手机</li>
                                    <li style="width:20%">邮箱</li>
                                    <li style="width:15%">科室</li>
                                    <li style="width:10%">医师回答</li>
                                    <li style="width:10%">修改</li>
                                    <li style="width:10%">删除</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul> 
                              
                                <?php
								foreach($data as $row)
								{
									echo '<ul data-id="'.$row['id'].'">
                                	<li style="width:10%">'.$row['alias'].'</li>
                                    <li style="width:20%">'.$row['phone'].'</li>
                                    <li style="width:20%">'.$row['email'].'</li>
                                    <li style="width:15%">'.$row['position'].'</li>
                                    <li style="width:10%"><a href="'.site_url('user/FquestionsByUid/'.$row['id']).'">查看('.$row['replyNums'].')</a></li>
									<li style="width:10%"><a href="'.site_url('counselor/edityishi/'.$row['id']).'">修改</a></li>
									<li style="width:10%"><a href="'.site_url('counselor/delmyishi/'.$row['id']).'">删除</a></li>
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