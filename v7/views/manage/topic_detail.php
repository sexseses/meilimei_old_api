<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" > 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">话题管理</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" >
                        	<div class="manage_search"><?php echo form_open('manage/tuijian'); ?>
                            	<ul> 
                                    <li>话题名称<input name="sname" type="text" value="<?php echo $this->input->post('sname') ?>"></li> 
                                    <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                </ul></form>
                            </div>
                        	<div class="manage_yuyue_form">
                                <div style="background:#F6F6F6;border:solid 1px #efefef; padding:3px;">
                                    <h3>话题信息：</h3>
                                    【标题】<?php $type_data = unserialize($minfo[0]->type_data); echo $type_data['title']; ?><br>
                                    【内容】<?php echo $minfo[0]->content;echo "(时间:".date("Y-m-d H:i",$minfo[0]->ctime).")"; ?><br>
									<?php 
										foreach($pictures as $r){
											echo '<img src="'.$r->savepath.'" width="200">';
										}
									?>
                                    <?php if( $extras['haspic']==1){ ?>
                                    相关图片：<img src="<?php echo $extras['url']; ?>">
                                    <?php }elseif($extras['haspic']==2){ ?> 
                                    相关图片：
                                    <?php foreach($extras['url'] as $t) {
									   echo '<img src="http://pic.meilimei.com.cn/upload/'.$t['savepath'].'"><br>'.$t['info'].'<br>' ;	
									}?>
                                    <?php } ?>
                                </div>
                            	<ul> 
                                    <li style="width:65%">评论</li>
                                    <li style="width:10%">评论者</li>
                                    <li style="width:10%">被评论者</li>
                                    <li style="width:10%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                               
                                <?php 
								foreach($results as $row){
								    if($row->phone=='虚拟用户'){
                                        echo '<ul>
                                  	<li style="width:65%">'.preg_replace('#\[([\x80-\xff]+)\]#is','http://www.meilimei.com/images/faces/$1.png',$row->comment).'</li>
                                  	<li style="width:10%">虚拟用户'.$row->alias.'</li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->comment.'?\')" href="'.base_url('manage/topic/commentdel/'.$row->id).'" >删除</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
                                    }else{
                                        $tdata = unserialize($row->data);

                                        $this->db->where('id',$row->touid);
                                        $touser = $this->db->get('users')->result_array();

                                        echo '<ul>
										<li style="width:10%">'.$row->id.'</li>
                                  	<li style="width:35%">'.$row->comment.'</li>';
                                    if(isset($tdata[0]['path'])){
                                        echo '<li style="width:20%"><img src="http://pic.meilimei.com.cn/upload/'.$tdata[0]['path'] .'" width="100px" height="100px"/></li>';
                                    }else{
                                        echo '<li style="width:20%"></li>';
                                    }
                                  	echo '<li style="width:10%"><a href="'.($row->role_id==3?site_url('manage/yiyuan/detail/'.$row->fuid):$row->role_id==2?site_url('manage/yishi/detail/'.$row->fuid):site_url('manage/users/detail/'.$row->fuid)).'">'.$row->phone.'</a></li>
                                   <li style="width:10%"><a href="'.($row->role_id==3?site_url('manage/yiyuan/detail/'.$row->touid):$row->role_id==2?site_url('manage/yishi/detail/'.$row->touid):site_url('manage/users/detail/'.$row->touid)).'">'.$touser[0]['username'].'</a></li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->comment.'?\')" href="'.base_url('manage/topic/commentdel/'.$row->id).'" >删除</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';

                                        if(!empty($row->clild)){
                                            echo '<pre>11111111111';
                                            print_r($row->clild);
                                        }
                                    }

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
                            <div class="manage_yuyue_form">
                           <?php  
							 foreach($doctor_comments as $row){ 
                                echo '<ul>
										<li style="width:10%">'.$row->id.'</li>
                                  	<li style="width:65%">'.$row->comment.'</li>
                                  	<li style="width:10%"><a href="'.($row->role_id==3?site_url('manage/yiyuan/detail/'.$row->fuid):$row->role_id==2?site_url('manage/yishi/detail/'.$row->fuid):site_url('manage/users/detail/'.$row->fuid)).'">'.$row->alias.'</a></li>
                                   <li style="width:10%"><a onclick="return confirm(\'确认删除 '.$row->comment.'?\')" href="'.base_url('manage/topic/commentdel/'.$row->id).'" >删除</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>';
								}
								?></div>
                               <?php   
							if($minfo[0]->q_id){
							?>相关医生回答
                            <?php
							$this->db->select('id, content, cdate'); 
							$this->db->where('qid', $minfo[0]->q_id); 
							$tmp = $this->db->get('wen_answer')->result(); 
							foreach($tmp as $r){
								 echo '<ul>
										<li style="width:10%">'.$r->id.'</li>
                                  	<li style="width:65%">'.$r->content.'</li>
                                  	<li style="width:10%">'.date('Y-m-d',$r->cdate).'</li>
                                </ul> <div class="clear" style="clear:both;"></div>';
							}
							?>
                            <?php } ?>

                            <div class="manage_search"><form method="get" action="<?php echo base_url('manage/topic/detail/'.$minfo[0]->weibo_id); ?>">
                                    <ul>
                                        <li>非windows<input name="wsource" type="hidden" value="windows"></li>
                                        <li><input name="submit" type="submit" value="搜索" class="search"></li>
                                    </ul></form>

                            </div>
                            <div class="comments" style="margin-bottom:50px"><?php echo form_open_multipart('manage/topic/comment/'.$tid); ?>

                            <ul style="padding:10px;">
                            <li><label style="width:100px; display:inline-block">回复评论的ID</label><input style="padding:2px;" value="0" type="text" name="commentTo" id="commentTo" /> </li>                         <li><label style="width:100px; display:inline-block">类型</label> 
                            <select id="commenttype" name="commenttype">
                            <option selected="selected" value="topic">话题</option>
                            <option value="ans">咨询</option>
                            </select>
                            </li>
                            <li><label style="width:100px; display:inline-block">用户id</label><input ="padding:2px;" type="text" id="fuid" name="fuid" value="" /></li>
                            <li><label style="width:100px; display:inline-block">图片</label> <input type="file" name="attachPic" /></li>
                                <li><label style="width:100px; display:inline-block">是否推送用户通知</label> <input type="checkbox" name="push" value="1"/></li>
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
	</div></div><input type="hidden" name="touid" value="<?php echo $minfo[0]->uid ?>" /></li> 
                            <li style="padding:10px 10px 10px 100px;"><input style="display:block" id="sendcomment" type="submit" name="submit" value="发布" style="padding:2px 10px;" /></li>
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