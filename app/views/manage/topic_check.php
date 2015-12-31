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
                                <li><a href="<?php echo site_url('manage/topic/order'); ?>">推荐排序</a></li>
                                <li><a href="<?php echo site_url('manage/topic/setting'); ?>">话题配置</a></li>
                                <li><a href="<?php echo site_url('manage/topic/comments'); ?>">评论</a></li>
                                <li><a href="<?php echo site_url('manage/topic/total'); ?>">统计</a></li>
                                <li class="on"><a href="<?php echo site_url('manage/topic/cktopic'); ?>">待审核</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	 
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:30%"><a id="selectall">[全部]</a><a id="selectnone">[取消]</a> 标题</li>  
                                    <li style="width:15%">内容</li>
                                    <li style="width:10%">发布者</li>
                                    <li style="width:10%">来源</li>
                                    <li style="width:10%">创建时间</li>
                                    <li style="width:10%">回复</li>
                                    <li style="width:10%">操作<a id="deleteid">删除</a> - <a id="passed">通过</a></li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){
								   $tmp = unserialize($row->type_data); 
									echo '<ul>
                                	<li style="width:30%"><input type="checkbox" value="'.$row->weibo_id.
									'" name="seclc[]" /> <a href="'.base_url('manage/topic/detail/'.$row->weibo_id).'" >'.$tmp['title'].'</a></li>
                                  	<li style="width:15%">'.$row->content.'</li><li style="width:10%"><a href="'.site_url('manage/users/detail/'.$row->uid).'">'.($row->alias!=''?$row->alias:$row->phone).'</a></li><li style="width:10%">'.$row->wsource.'</li>
   <li style="width:10%">'.date('Y-m-d H:i',$row->ctime).'</li><li style="width:10%">'.$row->comments.'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$tmp['title'].'?\')" href="'.base_url('manage/topic/del/'.$row->weibo_id).'" >删除</a>  <a href="'.base_url('manage/topic/detail/'.$row->weibo_id).'" >详细</a> <a href="'.base_url('manage/topic/edit/'.$row->weibo_id).'">编辑</a> </li> </ul> <div class="clear" style="clear:both;"></div>';
								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" >
                                   <?php echo $pagelink ?> 
                                </div>
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript">$(function(){
    $("#selectall").click(function(){ 
		$(".manage_yuyue_form :checkbox").attr("checked",true); 
    });
	 $("#selectnone").click(function(){ 
		$(".manage_yuyue_form :checkbox").attr("checked",false); 
    }); 
	 
	$("#passed").click(function(){
		$(".manage_yuyue_form :checkbox:checked").each(function(){
			 $(this).parent().parent().hide(300);
           $.get("../../manage/topic/passcheck/"+$(this).val(), {id: $(this).val()},     
           function (data, textStatus){     
           }, "json");
        })
	})
	$("#deleteid").click(function(){
	    if(confirm("确认删除？")){
		$(".manage_yuyue_form :checkbox:checked").each(function(){
			 $(this).parent().parent().hide(300);
           $.get("../../manage/topic/del/"+$(this).val(), {id: $(this).val()},     
           function (data, textStatus){     
           }, "json");
        })}
	})
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
