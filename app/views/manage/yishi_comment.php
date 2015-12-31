<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	 <li ><a href="<?php echo site_url('manage/yishi/detail/'.$acuid) ?>">医师基本资料</a></li> <li><a href="<?php echo site_url('manage/yishi/editpass/'.$acuid) ?>">修改密码</a></li>  <li class="on"><a href="<?php echo site_url('manage/yishi/yishi_comment/'.$acuid) ?>">评价</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                            <div class="manage_search"> 
                          <?php echo form_open('manage/yishi/yishi_comment/'.$acuid); ?>   
                            	<ul>  
                            <li><label style="width:100px; display:inline-block">用户</label><input style="padding:2px;" value="" type="text" name="user_id" id="user_id" /><input type="hidden" id="suser_id" name="fuid" value="" /></li>
                            <li style="margin-top:10px; clear:both;"><label style="width:100px; display:inline-block">内容</label> <textarea style="padding:1px;width:500px;height:30px;" name="comment"></textarea> </li> 
                            <li style="margin-top:10px; clear:both;"><label style="width:100px; display:inline-block">评分</label> 
                            <select name="score">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            </select>
                             </li> 
                            <li style="padding:10px 10px 10px 100px;clear:both;"><input style="display:none" id="sendcomment" type="submit" name="submit" value="发布" style="padding:2px 10px;"
                                </ul></form> 
                            </div>
                        	<div class="manage_yuyue_form">
                            	<ul>
                                	<li style="width:10%"><a id="selectall">[全部]</a> ID</li>  <li style="width:10%">用户</li>
                                    <li style="width:30%">内容</li><li style="width:10%">评分</li>
                                    <li style="width:15%">时间</li>
                                    <li style="width:10%"><a id="deltall">[删除]</a> 操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <?php  
								foreach($results as $row){ 
									echo '<ul>
                                	<li style="width:10%"><input type="checkbox" value="'.$row['id'].
									'" name="seclc[]" /> '.$row['id'].'</li><li style="width:10%">'.$row['showname'].'</li>
                                  	 <li style="width:30%">'.$row['review'].'</li> <li style="width:10%">'.$row['score'].'</li>
                                  	<li style="width:15%">'.$row['created'].'</li>
                                   </ul> <div class="clear" style="clear:both;">  </div>';
								}
								?>
                         
                              
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                
                            </div>
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> <script type="text/javascript"> 
	$(function () {
    
        $("#selectall").click(function () {
            $(".manage_yuyue_form :checkbox").attr("checked", true);
        });
        $("#deltall").click(function () { 
            $(".manage_yuyue_form :checkbox:checked").each(function () {
                $(this).parent().parent().hide(300);
                $.get("http://www.meilimei.com/manage/yishi/cdel/" + $(this).val(), {
                    id: $(this).val()
                }, function (data, textStatus) {}, "json");
            })
        })
    });
 </script><script>
  $(function() { 
	$("#user_id").autocomplete({
                source: "../../topic/Suser",
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
