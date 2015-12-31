<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> 
    <?php  $this->load->view('manage/leftbar'); ?>
          <div class="manage_center_right">
              <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/community')?>">社区活动管理</a></li>
                    <li ><a href="<?php echo base_url('manage/community/add')?>">添加社区活动</a></li>
                    <li class="on"><a href="">社区活动报名详情</a></li>
                </ul>
              </div>
                <div class="manage_yuyue" >
                  <div class="manage_yuyue_form">
                    <form action="/manage/community/sendAllSmsone" method="post">
                      <input type="hidden" name="event_id" value="<?php echo $event_id; ?>"/>
                      <ul> 
                        <li style="width:5%"><input type="checkbox"  name="SelectAll" id="SelectAll" onclick="selectAll();"></li> 
                        <li style="width:5%">ID</li> 
                        <li style="width:10%">名字</li>
                        <li style="width:10%">昵称</li> 
                        <li style="width:10%">手机</li>
                        <li style="width:5%">城市</li>
                        <li style="width:10%">参加时间</li>
                        <li class="width:30%">操作</li>
                        <div class="clear" style="clear:both;"></div>
                      </ul>
                      <?php  
                          foreach($coll_rs as $row){
                            $sms = '<a onclick="#">未发送短信</a>';
                            if($row->sms == 'Y'){
                              $sms = '<a title="'.$row->smscontext.'">查看短信</a>';
                            }
                            echo '  <ul class=""><li style="width:5%"><input type="checkbox" value="'.$row->id.'" name="ids[]" ></li> 
                                    <li style="width:5%">'.$row->id .'</li>
                                    <li style="width:10%"><a target="_blank" href="'.base_url('manage/users/detail/'.$row->user_id).'">'.$row->username .'</a></li>
                                    <li style="width:10%">'.$row->realname .'</li>
                                    <li style="width:10%">'.$row->mobile .'</li>
                                    <li style="width:5%">'.$row->city .'</li>
                            	      <li style="width:10%">'.(empty($row->creattime)?'无':date('Y-m-d',$row->creattime)).'</li>
    									              <li style="width:30%">'.$sms.'<li/>
                                    <div class="clear" style="clear:both;"></div></ul>
                                  ';
                          }
								      ?>
                        <div class="clear" style="clear:both;"></div>
                        <div class="paging_left" >
                              <textarea name="smstext" id="smstext" rows="5" cols="50"> </textarea>
                              <input type="submit" value="发送短信"> 
                        </div>
                  </div>
                      <div class="paging">
                          
                      </div>
                 </form>     
              </div>
        </div>
  <div class="clear" style="clear:both;"></div>
</div>
<script>
function selectAll(){  
    if ($("#SelectAll").attr("checked")) {  
        $(":checkbox").attr("checked", true);  
    } else {  
        $(":checkbox").attr("checked", false);  
    }  
}	 
</script>