<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">推荐管理</a>(点击权重数字编辑,按回车键(Enter)保存)</li>
                            </ul>
                        </div>
                        <div class="tuijian_tab"> 
                            <ul>
                               <a href="<?php echo site_url('manage/tuijian') ?>"> <li <?php echo($this->uri->segment(3) != 'yiyuan' ) ?'class="on"':''; ?>>推荐医师</li></a>
                               <a href="<?php echo site_url('manage/tuijian/yiyuan') ?>"><li <?php echo  $this->uri->segment(3) == 'yiyuan' ?'class="on"':''; ?>>推荐医院</li></a>
                            </ul>
                        </div>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/tuijian'); ?>
                            	<ul> 
                                    <li>医生名称<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>"></li>
                                     <li>城市<input name="city" type="text" value="<?php echo $this->input->post('city') ?>"></li>
                                    <li>手机<input name="phone" type="text" value="<?php echo $this->input->post('phone') ?>"></li>
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li class="Vertical17">医生姓名</li> 
                                    <li class="Vertical19">手机</li>
                                    <li class="Vertical20">Email</li>
                                    <li class="Vertical21">城市</li>
                                    <li class="Vertical22">电话</li> 
                                    <li class="Vertical24">权重</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php 
								foreach($data as $row){
									echo '<ul>
                                	<li class="Vertical17">'.$row['alias'].'</li> 
                                    <li class="Vertical19">'.$row['phone'].'</li>
                                    <li class="Vertical20">'.$row['email'].'</li>
                                    <li class="Vertical21">'.$row['city'].'</li>
                                    <li class="Vertical22">'.$row['tel'].'</li>
                                   <li class="Vertical09 editamount" data-id="'.$row['id'].'">'.$row['rank_search'].'</li>
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
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($data)-1 ?>个，共<?php echo $total_rows ?>个</h5>
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/tuijianset', {"dataid":dataid,"weight":amount}, function(data) {  
   });}</script>
  </div>
</div>
