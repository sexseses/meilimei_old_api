<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
      <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/community')?>">社区活动管理</a></li>
                    <li ><a href="<?php echo base_url('manage/community/add')?>">添加社区活动</a></li>
                    <li class="on"><a href="">社区活动发帖详情</a></li>
                </ul>
              </div>
                  <div class="manage_yuyue" >
                        	<form action="/manage/community/sendAllSmstwo" method="post">
                            <input type="hidden" name = "event_id" value="<?php echo $event_id; ?>"/>
                        	<div class="manage_yuyue_form">
                            	<ul><li style="width:5%"><input type="checkbox"  name="SelectAll" id="SelectAll" onclick="selectAll();"></li>  
                                <li style="width:5%">ID</li> 
                                    <li style="width:30%">帖子标题</li> 
                                    <li style="width:15%">发帖时间</li>
                                    <li style="width:10%">发帖用户</li>
                                    <li style="width:5%">置顶</li>
                                    <li style="width:5%">显示</li>
                                    <li class="width:20%">操作</li>
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                  <?php  
                                      foreach($coll_rs as $row){
                                         $top = '<a onclick="return topic_top('.$row->topic_id.')">置顶</a>';
                                         $display = '<a onclick="return topic_display('.$row->topic_id.')">显示</a>';
                                         if($row->top){
                                            $top= '<a onclick="return topic_no_top('.$row->topic_id.')">取消置顶</a>';
                                         }
                                         if($row->display){
                                            $display = '<a onclick="return topic_no_display('.$row->topic_id.')">隐藏</a>';;
                                         }
                                         $sms = '<a onclick="#">未发送短信</a>';
                                         if($row->sms == 'Y'){
                                            $sms = '<a title="'.$row->smscontext.'">查看短信</a>';
                                         }

                                         echo ' <ul class=""><ul class=""><li style="width:5%"><input type="checkbox" value="'.$row->id.'" name="ids[]" ></li>
                                    	   <li style="width:5%">'.$row->id .'</li>
                                         <li style="width:30%"><a target="_blank" href="'.base_url('manage/topic/detail/'.$row->weibo_id).'">'.mb_substr($row->topic_type,0,20) .'</a></li>
                            	           <li style="width:15%">'.(empty($row->ctime)?'无':date('Y-m-d',$row->ctime)).'</li>
                                         <li style="width:10%"><a target="_blank" href="'.base_url('manage/users/detail/'.$row->userid).'">'.$row->alias.'</a></li>
    								                     <li style="width:5%">'.$top.'</li>
                                         <li style="width:5%">'.$display.'</li>
    									                   <li style="width:20%">'.$sms.'<li/>
                                         <div class="clear" style="clear:both;"></div></ul>';
                                     }
								                  ?>
                                <div class="clear" style="clear:both;"></div>
                                <textarea name="smstext" id="smstext" rows="5" cols="50"> </textarea>
                                <input type="submit" value="发送短信">
                            </div>
                            <div class="paging">
                                <div class="paging_left" >
                                   
                                </div>
                            </div>
                        </div>
                        </form>  
  </div>
  <div class="clear" style="clear:both;"></div>
</div>
<script>
function topic_top(id){
  var param = new function() {
    this.topic_id = id;
  };
  if(confirm("确认该条置顶?")){
    $.ajax({
      type : 'post',
      url: 'http://www.meilimei.com/manage/ajaxfun/community_detail_top',
      data : param,
      dataType : "json",
      success : function(result) {
        if(result.code=='0'){
            alert(result.msg);
        }else if(result.code=='1'){
            alert(result.msg);
        }
      },
      error : function(result, status, errorThrown) {
        alert(result);
        execCommonAjaxError(result, status, errorThrown);
      }
    });
  }else{
      return false;
  }
}

function topic_no_top(id){
  var param = new function() {
    this.topic_id = id;
  };
  if(confirm("取消该条置顶?")){
    $.ajax({
      type : 'post',
      url: 'http://www.meilimei.com/manage/ajaxfun/community_detail_notop',
      data : param,
      dataType : "json",
      success : function(result) {
        if(result.code=='0'){
            alert(result.msg);
        }else if(result.code=='1'){
            alert(result.msg);
        }
      },
      error : function(result, status, errorThrown) {
        alert(result);
        execCommonAjaxError(result, status, errorThrown);
      }
    });
  }else{
      return false;
  }
}

function topic_display(id){
  var param = new function() {
    this.topic_id = id;
  };
  if(confirm("确认该条显示?")){
    $.ajax({
      type : 'post',
      url: 'http://www.meilimei.com/manage/ajaxfun/community_detail_nodisplay',
      data : param,
      dataType : "json",
      success : function(result) {
        if(result.code=='0'){
            alert(result.msg);
        }else if(result.code=='1'){
            alert(result.msg);
        }
      },
      error : function(result, status, errorThrown) {
        alert(result);
        execCommonAjaxError(result, status, errorThrown);
      }
    });
  }else{
      return false;
  }
}

function topic_no_display(id){
  var param = new function() {
    this.topic_id = id;
  };
  if(confirm("确认该条隐藏?")){
    $.ajax({
      type : 'post',
      url: 'http://www.meilimei.com/manage/ajaxfun/community_detail_display',
      data : param,
      dataType : "json",
      success : function(result) {
        if(result.code=='0'){
            alert(result.msg);
        }else if(result.code=='1'){
            alert(result.msg);
        }
      },
      error : function(result, status, errorThrown) {
        alert(result);
        execCommonAjaxError(result, status, errorThrown);
      }
    });
  }else{
      return false;
  }
}

function selectAll(){  
    if ($("#SelectAll").attr("checked")) {  
        $(":checkbox").attr("checked", true);  
    } else {  
        $(":checkbox").attr("checked", false);  
    }  
}  
 
			 
</script>