<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/questions/detail/'.$qid) ?>">医师回答>交谈信息</a></li>
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
                            	 
                                <?php 
								
								echo '<ul><li style="width:10%">医师</li>   <li style="width:25%">'.(date('Y-m-d H:i',$qans[0]->cdate)).'</li>  
                                  	<li style="width:55%">'.$qans[0]->content.'</li> 
                                 
                                </ul> <div class="clear" style="clear:both;"></div>';
								foreach($results as $row){
								 
									echo '<ul><li style="width:10%">'.$row['urole'].'</li>   <li style="width:25%">'.$row['cTime'].'</li>  
                                  	<li style="width:55%">'.$row['content'].'</li> 
                                 
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
                    <div class="comments" style="margin-bottom:50px">
                        <?php echo form_open('manage/questions/comment/'.$tid); ?>
                        <ul style="padding:10px;">
                            <li><label style="width:100px; display:inline-block">用户名</label><input style="padding:2px;" value="" type="text" name="user_id" id="user_id" /><input type="hidden" id="suser_id" name="fuid" value="<?php echo $preview ?>" /></li>
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/tuijianset', {"dataid":dataid,"weight":amount}, function(data) {  
   });}</script>
      <script>
          $(function() {
              $("#user_id").autocomplete({
                  source: "/manage/questions/Suser",
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
