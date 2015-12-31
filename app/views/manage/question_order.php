<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('manage/questions/newest')?>">咨询管理</a></li><li><a href="<?php echo site_url('manage/questions/newest')?>">最新信息</a></li>     </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search">  
                                <select onchange="window.location.href=this.options[selectedIndex].value"><option>分类</option><option value="http://www.meilimei.com/manage/questions/order/1">未回答咨询</option><option value="http://www.meilimei.com/manage/questions/order/2">未终结咨询</option></select>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul><li style="width:10%"><a id="selectall">[全部]</a><a id="selectnone">[取消]</a> 用户名</li>
                                    <li style="width:15%">邮箱/手机</li>
                                	<li style="width:20%">标题</li>
                                    <li style="width:10%">咨询时间</li>
                                    <li style="width:15%">回答医生</li>
                                    <li style="width:8%">状态</li>
                                    <li style="width:7%">系统</li>
                                    <li style="width:8%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){ 
 									echo '<ul><li style="width:10%"><input type="checkbox" value="'.$row->id.
									'" name="seclc[]" /> '.$row->alias.'</li>
 									<li style="width:15%"><a href="'.base_url('manage/users/detail/'.$row->uid).'">'.($row->phone==''?$row->email:$row->phone).'</a></li>
                                	<li style="width:20%">'.$row->title.'</li>
                                  	<li style="width:10%">'.date('Y-m-d H:i',$row->cdate).'</li>
                                  	<li style="width:15%">'.$row->doctors.'</li><li style="width:8%">'.($row->has_complete==flase?'未完成':'完成').'</li>
									<li style="width:7%">'.$row->device.'</li>
                                   <li style="width:8%"><a onclick="return confirm(\'确认删除 '.$row->title.'?\')" href="'.base_url('manage/questions/del/'.$row->id).'" >删除</a>  <a href="'.base_url('manage/questions/detail/'.$row->id).'" >详细</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right" style="<?php echo $issubmit?'display:none':'' ?>">
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
	$("#deleteid").click(function(){
		$(".manage_yuyue_form :checkbox:checked").each(function(){
			 $(this).parent().parent().hide(300);
           $.get("../manage/questions/del/"+$(this).val(), {id: $(this).val()},     
           function (data, textStatus){     
           }, "json");
        })
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
