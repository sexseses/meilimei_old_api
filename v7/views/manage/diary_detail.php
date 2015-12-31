<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" > 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    <div class="question_nav">
                        <ul>
                            <li class="on"><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                            <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                            <li><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                            <li><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加目录</a></li>
                            <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                            <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                            <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                        </ul>
                    </div>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >

                        	<div class="manage_yuyue_form">
                                <div style="background:#F6F6F6;border:solid 1px #efefef; padding:3px;">
                                    <h3>美人计：</h3>
                                    【标题】<?php echo $noteInfo[0]->content; ?><br>
                                    【内容】<?php echo "(时间:".date("Y-m-d H:i",$noteInfo[0]->created_at).")"; ?><br>
                                     <?php
									   echo '<img src="'.$noteInfo[0]->imgurl.'"/>';
                                     ?>

                                </div>
                            	<ul> 
                                    <li style="width:10%">评论</li>
									 <li style="width:45%">评论内容</li>
                                    <li style="width:10%">评论者</li>
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                               
                                <?php 
								foreach($results as $row){

                                    echo '<ul><li style="width:10%">'.$row->cid.'</li>
                                  	<li style="width:45%">'.$row->content.'</li>
                                  	<li style="width:10%">'.$row->fromusername.'</li>';
                                  	if($row->ncimgurl){
                                  	    echo '<li style="width:10%"><img src="'.$row->ncimgurl .'" width="100px" height="100px"/></li>';
                                    }else{
                                        echo '<li style="width:10%"></li>';
                                    }
                                    echo '<li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->content.'?\')" href="'.base_url('manage/diary/commentdel/'.$row->cid).'?nid='.$row->nid.'" >删除</a></li>
                                    </ul> <div class="clear" style="clear:both;"></div>';


								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right"  >
                                    <ul>
                                        <li><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div><div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="manage_search"><form method="get" action="<?php echo base_url('manage/diary/detail/'.$noteInfo[0]->nid); ?>">
                                    <ul>
                                        <li>非windows<input name="wsource" type="hidden" value="windows"></li>
                                        <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                    </ul></form>

                            </div>

                            <?php echo form_open_multipart('manage/diary/addZan/'.$tid); ?>
                            <ul style="padding:10px;">
                                <li><label style="width:100px; display:inline-block">用户id</label><input type="text" id="suid" name="suid" value="" /></li>
                                <li style="padding:10px 10px 10px 100px;"><input type="submit" name="submit" value="点赞" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>


                            <div style="WORD-BREAK: break-all; WORD-WRAP: break-word;">

                                <?php
                                $str = '';
                                if(!empty($zanUserList)){
                                    foreach($zanUserList as $item){
                                        if($str == ''){
                                            $str = $item;
                                        }else{
                                            $str .= ','.$item;
                                        }
                                    }
                                }

                                echo $str;
                                ?>
                            </div>


                            <div class="comments" style="margin-bottom:50px"><?php echo form_open_multipart('manage/diary/sendcomment/'.$noteInfo[0]->nid); ?>
                          
                            <ul style="padding:10px;">
                            <li><label style="width:100px; display:inline-block">回复评论的ID</label><input style="padding:2px;" value="0" type="text" name="commentTo" id="commentTo" /> </li>
                            <li><label style="width:100px; display:inline-block">用户id</label><input type="text" id="suser_id" name="fuid" value="" /></li>
                            <li><label style="width:100px; display:inline-block">图片</label> <input type="file" name="attachPic" /></li><li><label style="width:100px; display:inline-block">是否推送用户通知</label> <input type="checkbox" name="push" value="1"/></li>
                            <li style="margin-top:10px; height:auto;position:relative; "><label style="width:100px; display:inline-block">内容</label> <textarea id="rl_exp_input" style="padding:1px;width:500px;height:50px;" name="comment"></textarea> <div style="position:relative;width:100%;padding-left:120px;float:left">
                            <a href="javascript:void(0);" id="rl_exp_btn">表情</a>
                            <div class="rl_exp" id="rl_bq" style="display:none;">
                            <ul class="rl_exp_tab clearfix">
                            <li><a href="javascript:void(0);" class="selected">默认</a></li>
                            <li><a href="javascript:void(0);">拜年</a></li>
                            </ul>
                                <ul class="rl_exp_main clearfix rl_selected"></ul>
                                <ul class="rl_exp_main clearfix" style="display:none;"></ul>
                                <a href="javascript:void(0);" class="close">×</a>
                                <li style="padding:10px 10px 10px 100px;"><input id="sendcomment" type="submit" name="submit" value="发布" style="padding:2px 10px;" /></li>
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
   });}</script><script>
  $(function() {  
  $("#commenttype").val('topic');
    var types = 'topic';
	$("#user_id").autocomplete({
                source: "../../topic/Suser?type="+types,
                minLength: 2,
                select: function(event, ui) { 

                    $('#suser_id').val(ui.item.id);  
					$("#sendcomment").show();
                }
  });
      $("#uid").autocomplete({
          source: "../../topic/Suser?type="+types,
          minLength: 2,
          select: function(event, ui) {

              $('#suid').val(ui.item.id);
          }
      });
  $("#commenttype").live("change",function(){   
	    types = $(this).val();
		$("#sendcomment").hide(); 
		$("#user_id").autocomplete({
                source: "../../topic/Suser?type="+types,
                minLength: 2,
                select: function(event, ui) { 
                    $('#suser_id').val(ui.item.id);  
					$("#sendcomment").show();
                }
 }); 
	})
	

  }); 
  </script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/rl_exp.js"></script>  
  </div>
</div>
