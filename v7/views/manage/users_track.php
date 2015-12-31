<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="selected"><a href="<?php echo site_url('manage/users/detail/'.$uid) ?>">用户基本资料</a></li><li><a href="<?php echo site_url('manage/users/editpass/'.$uid) ?>">修改密码</a></li></a>  
                                <li class="on"><a href="<?php echo site_url('manage/users/track/'.$uid) ?>">跟踪纪录</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form"> 
                               <form method="post">
                               <?php echo form_open(); ?>
                               <input type="hidden" value="<?php echo $user[0]->uid ?>" name="touid" /><ul>
                               <li style="clear:both;">状态</li><li><label><input type="radio" name="states" value="0">未联系</label>
                               <label><input type="radio" name="states" value="2">待联系 </label>
                              <label> <input type="radio" name="states" checked="checked" value="1">已联系</label></li>
                               <li style="clear:both;">下次时间</li><li><input name="nDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li>
                               <li style="clear:both;">备注</li><li><textarea name="remark" style="width:500px;height:50px"></textarea></li>
                              <li style="clear:both;">  <button type="submit" style="height:25px;width:100px;" name="submit">添加</button></li></ul>
                               </form>
                            	</div>  
                                <div class="clear" style="clear:both;"></div>
                                <dl>
                                <?php
								function cstate($s){
									switch($s){
										case 0:
										  return '未联系'; 
										case 1:
										  return '已联系'; 
										case 2:
										  return '待联系'; 
									}
								}
					  foreach($res as $r){
					 	echo '<dd style="height:30px;padding:2px 10px;" class="clear"><label>'. $r['alias'] .': </label>[状态]'.cstate($r['state']).'; '.$r['remark'].($r['nxtdate']>0?' - 下次联系日期:'.date('Y-m-d',$r['nxtdate']):'').' - 填写日期:'.date('Y-m-d H:i:s',$r['cdate']).'</dd>';
								}
								?>
                                </dl><div class="clear" style="clear:both;"></div>
                            </div>
                    </div><script type="text/javascript">$(function() {
                    $(".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();  }) 
                    </script>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
