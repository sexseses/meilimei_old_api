<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/topic'); ?>">话题管理</a></li>
                                <li><a href="<?php echo site_url('manage/topic/add'); ?>">添加话题</a></li>
                                <li><a href="<?php echo site_url('manage/topic/nocla'); ?>">未分类</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/topic/order'); ?>">推荐排序</a></li>
                                <li><a href="<?php echo site_url('manage/topic/setting'); ?>">话题配置</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >   
                            <div class="comments"><?php echo form_open('manage/topic/setting/'); ?> 
                            <ul style="padding:10px;"><li>
                            <label style="width:100px; display:inline-block">发帖积分</label><input name="addtopic" type="text" style="padding:2px;" value="<?php echo $add ?>" size="45" /></li>
                           <li>
                            <label style="width:100px; display:inline-block">回帖积分</label><input name="rtopic" type="text" style="padding:2px;" value="<?php echo $reply ?>" size="45" /></li>
                            <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="修改" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
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
