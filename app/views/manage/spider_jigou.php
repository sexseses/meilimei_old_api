<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/spider'); ?>">机构临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/topic'); ?>">话题临时数据</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gjigou'); ?>">抓取机构</a></li>
                                <li><a href="<?php echo site_url('manage/spider/gtopic'); ?>">抓取话题</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                         
                        	<div class="manage_yuyue_form">
                            	<ul>
                                    <li style="width:20%">来源</li> 
                                	<li style="width:45%">标题</li>  
                                    <li style="width:15%">内容</li> 
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($res as $row){ 
									echo '<ul>
                                	<li style="width:20%"><a target="_blank" href="http://www.dianping.com'.$row['url'].'" >'.$row['url'].'</a></li>
                                  	<li style="width:45%">'.$row['title'].'</li>
                                  	<li style="width:15%">'.date('Y-m-d H:i',$row['cdate']).'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row['id'].'?\')" href="'.base_url('manage/spider/jigouidel/'.$row['id']).'" >删除</a>   <a href="'.base_url('manage/spider/jigouEdit/'.$row['id']).'">编辑</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?> 
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                               <?php echo $pagelink ?> 总数<?php echo $ac_rows ?>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript">$(function(){
    
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/tuijianset', {"dataid":dataid,"weight":amount}, function(data) {  
   });}</script>
  </div>
</div>
