<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/topic'); ?>">话题管理</a></li>
                                <li><a href="<?php echo site_url('manage/topic/add'); ?>">添加话题</a></li>
                                <li><a href="<?php echo site_url('manage/topic/nocla'); ?>">未分类</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/topic/order'); ?>">推荐排序</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:20%">标题</li>  
                                    <li style="width:25%">内容</li>
                                    <li style="width:10%">发布者</li>
                                    <li style="width:10%">来源</li>
                                     <li style="width:10%">排序</li>
                                    <li style="width:10%">创建时间</li>
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){
								   $tmp = unserialize($row->type_data); 
									echo '<ul>
                                	<li style="width:20%"><a href="'.base_url('manage/topic/detail/'.$row->weibo_id).'" >'.$tmp['title'].'</a></li>
                                  	<li style="width:25%">'.$row->content.'</li><li style="width:10%"><a href="'.site_url('manage/users/detail/'.$row->uid).'">'.($row->alias!=''?$row->alias:$row->phone).'</a></li><li style="width:10%">'.$row->wsource.'</li><li style="width:10%" class="Vertical09 editamount" data-id="'.$row->weibo_id.'">'.$row->weight.'</li>
                                  	<li style="width:10%">'.date('Y-m-d H:i',$row->ctime).'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$tmp['title'].'?\')" href="'.base_url('manage/topic/del/'.$row->weibo_id).'" >删除</a>  <a href="'.base_url('manage/topic/detail/'.$row->weibo_id).'" >详细</a> <a href="'.base_url('manage/topic/edit/'.$row->weibo_id).'">编辑</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
                                    <ul>
                                        <li><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div>
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/topicOrder', {"dataid":dataid,"weight":amount}, function(data) {  
   });}
   </script>
  </div>
</div>
