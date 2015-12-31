<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/questions') ?>">咨询管理>医师回答</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/questions'); ?>
                            	<ul> 
                                    <li>咨询名称<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>"></li> 
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div>
                        	<div class="manage_yuyue_form">
				 
                            <div><h5>问题信息</h5><br />【标题】<?php echo $qresults[0]->title ?> <br /> 【内容】<?php echo $qresults[0]->description  ;if(!empty($attaches)) echo '<br><img src="'.$attaches[0]['imgfile'].'"/>';?>(时间:<?php echo date('Y-m-d H:i',$qresults[0]->cdate)?>) <br />咨询的医生: <a href="<?php echo site_url('manage/yishi/detail/'.$qresults[0]->toUid)?>"><?php echo $qresults[0]->alias ?></a> 
                            <br /> <?php
								   $tmp = $this->db->query("SELECT id FROM  `yuyue` where userby={$qresults[0]->fUid} ")->result_array();
								   if(!empty($tmp)){
								   ?>
                                    <a href="<?php echo site_url('manage/home/detail/'.$tmp[0]['id']) ?>" style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" type="button">客户记录</a>         <?php }else{ ?>
 <a href="<?php echo site_url('manage/home/addyuyue/'.$qresults[0]->fUid) ?>" style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" type="button">添加预约</a>
                                    <?php } ?>
                            </div>
                            	<ul> <li style="width:15%">用户名</li>
                                    <li style="width:25%">邮箱/手机</li>
                                    <li style="width:45%">回答</li> 
                                    <li style="width:8%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($results as $row){
								 
									echo '<ul> <li style="width:12%">'.$row->alias.'</li> <li style="width:27%;padding:5px;overflow:hidden"><a href="'.base_url('manage/users/detail/'.$row->uid.'/view').'">'.($row->phone==''?$row->email:$row->email).'</a></li> 
                                  	<li style="width:45%">'.$row->content.'(时间:'.date('Y-m-d H:i:s',$row->cdate).')</li> 
                                   <li style="width:8%"><a onclick="return confirm(\'确认删除 '.$row->content.'?\')" href="'.base_url('manage/questions/commentdel/'.$row->id).'" >删除</a> <a href="'.base_url('manage/questions/commentview/'.$qid.'/'.$row->uid).'">详细</a></li>
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
                                </div>
                            </div>
                        </div>
                        
                        
                     <div class="comments" style="margin-bottom:50px"><?php echo form_open('manage/questions/comment/'.$tid); ?> 
                            <ul style="padding:10px;"><li><label style="width:100px; display:inline-block">用户名</label><input style="padding:2px;" value="" type="text" name="user_id" id="user_id" /><input type="hidden" id="suser_id" name="fuid" value="<?php echo $preview ?>" /></li>
                            <li style="margin-top:10px;"><label style="width:100px; display:inline-block">内容</label> <textarea style="padding:1px;width:500px;height:50px;" name="comment"></textarea>                 <input type="hidden" name="touid" value="<?php echo $minfo[0]->uid ?>" /></li> 
                            <li style="padding:10px 10px 10px 100px;"><input style="display:none" id="sendcomment" type="submit" name="submit" value="发布" style="padding:2px 10px;" /></li>
                            </ul>
                            </form>
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
}); </script><script>
  $(function() { 
	$("#user_id").autocomplete({
                source: "../../questions/Suser",
                minLength: 2,
                select: function(event, ui) { 
                    $('#suser_id').val(ui.item.id);  
					$("#sendcomment").show();
                }
 });

  }); 
  </script>
  </div>
</div>
