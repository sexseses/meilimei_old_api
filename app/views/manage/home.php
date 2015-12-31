<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="http://www.meilimei.com/manage/home.html">客户记录</a></li><li><a href="<?php echo site_url('manage/home/tongji') ?>">统计</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
								<div class="manage_search">
								<form action="<?php echo base_url() ?>manage/search" method="get">
                            	<ul>  
                                    <li>  会员ID<input name="ID" type="text" value="<?php  echo $this->input->get('ID'); ?>"></li>
                                  <li class="f5 cotuselect">位置
                               <select class="prov" name="province" id="province"> 
                                </select> <select class="city" name="city" id="city">  </select>
                                </li>
                                    <li>医院<input name="yy" value="<?php  echo $this->input->get('yy'); ?>" type="text"></li>
                                    <li>姓名<input name="name" value="<?php  echo $this->input->get('name'); ?>" type="text"></li> 
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $this->input->get('yuyueDateStart'); ?>" class="datepicker">   </li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $this->input->get('yuyueDateEnd'); ?>" class="datepicker"></li> <li>手术状态<select name="ssstate"><option ></option><option <?php   echo $this->input->get('ssstate')=='否'?'selected="selected"':'';   ?> value="否">否</option><option value="是" <?php if($this->input->get('ssstate')) { echo $this->input->get('ssstate')=='是'?'selected="selected"':''; } ?>>是</option></select>   </li>
                                    <li>手机<input name="phone" value="<?php  echo $this->input->get('phone'); ?>" type="text"></li>
                                    <li>顾客类型<select name="gktype"><option></option>
                                    <option <?php   echo $this->input->get('gktype')==1?'selected="selected"':''; ?> value="1">无效客户</option>
                                    <option <?php   echo $this->input->get('gktype')==2?'selected="selected"':''; ?> value="2">一般客户</option>
                                    <option <?php   echo $this->input->get('gktype')==3?'selected="selected"':''; ?> value="3">重点客户</option> 
                                <option value="4" <?php echo $this->input->get('gktype')==4?'selected="selected"':'' ?>>已手术</option>
                                <option value="5" <?php echo $this->input->get('gktype')==5?'selected="selected"':'' ?>>需核实</option>
                                    </select></li> <Li>今日需联系用户<input <?php if($this->input->get('tnc')) { echo 'checked="checked"'; } ?> style="width:20px" name="tnc" type="checkbox" /></Li><Li>新留言<input name="newmessage"  <?php if($this->input->get('newmessage')) { echo 'checked="checked"'; } ?>  style="width:20px" type="checkbox" /></Li>
                                    <li>重单<input  <?php if($this->input->get('chongdan')) { echo 'checked="checked"'; } ?>  style="width:20px" name="chongdan" type="checkbox" /></li>
                                    <li>未派单<input  <?php if($this->input->get('nop')) { echo 'checked="checked"'; } ?>  style="width:20px" name="nop" type="checkbox" /></li>
                                    <li>已派单<input  <?php if($this->input->get('ysp')) { echo 'checked="checked"'; } ?>  style="width:20px" name="ysp" type="checkbox" /></li>
                                    <li>人员
                                    <select name="ome"> 
                                    <option value="0">全部</option>
                                    <?php
									foreach($managers as $r){
										echo '<option '.($this->input->get('ome') ==$r['id']?'selected="selected"':'').' value="'.$r['id'].'">'.$r['alias'].'</option>';
									}
									?>
                                   </select> </li>
                                    <li>结算月<input name="jiesuan" type="text" value="<?php  echo $this->input->get('jiesuan'); ?>" class="jiesuanDate"> </li>
                                    <li><input name="" type="submit" value="搜索" class="search"></li>
                                </ul>
                                </form> 
                            	</div> 
                                 <?php  
								 $ctstate = array('待确认','已确认');
								function getuser($sn,&$db,&$manager){
									$tmp = $db->query("SELECT company.name,company.userid,fuid from company LEFT JOIN yuyueSend ON yuyueSend.uid = company.userid WHERE yuyueSend.sn = '{$sn}'")->result_array();
									$h = '';  
									foreach($tmp as $r){
										$uid = $r['fuid']; 
										$r['name']&&$h.='<a href="'.site_url('manage/yiyuan/detail/'.$r['userid']).'" target="_blank">'.$r['name'].'</a> | ';
									}
									$manager = $db->query("SELECT username,alias,id from users WHERE id = '{$uid}'")->result_array();  
									return $h;
								}
								 
                                function DS($res,$city){
									$html = '';
									foreach($res as $r){
                                      $r['city']==$city&&$html .='<option value="'.$r['userid'].'">'.$r['name'].'</option>';
                                    }
									return $html;
								}$i=0;
								$mifno= '';
								foreach($data as $row)
								{$i++;  
								$manager = $this->db->query("SELECT username,alias,id from users WHERE id = '{$row['fuid']}'")->result_array();
								switch($row['role_id']){
									  case 1:
									     $link = site_url('manage/users/detail/'.$row['userto']);
									   break;
									   case 2:
									     $link = site_url('manage/yishi/detail/'.$row['userto']);
									   break;
									   default  :
									     $link = site_url('manage/yiyuan/detail/'.$row['userto']); 
								   } 
								   if($row['userby']!=0){
									   if($row['name']==''){
										   $wlink = $row['name'];
									   }else{
										   $wlink = '<a href="'.site_url('manage/users/detail/'.$row['userby']).'">'.$row['funame'].'<a>' ;
									   }
								   }else{
									   $wlink =  $row['name'];
								   } 
									echo '<div style="margin:0px 0px 15px 0px;">
									<ul class="nav_ul"><li style="width:8%;">'.($row['userby']?'ID:'.$row['userby']:'未注册用户').'</li><li style="width:15%;">姓名:'.$wlink.'</li><li style="width:15%;">手机:'.$row['phone'].'</li><li style="width:25%;">'.($row['userto']?'<a style="color:#CF3" href="'.site_url('manage/yiyuan/detail/'.$row['userto']).'">预约：'.$row['alias']:'').'</a></li><li style="width:20%;" > 
<a class="paidan" href="'.site_url('manage/home/paidan/'.$row['id']).'">派单</a> <a class="paidan" href="'.site_url('manage/home/paidan_track/'.$row['sn']).'">跟踪</a>  <a class="paidan" href="'.site_url('manage/home/detail/'.$row['id']).'">查看详细</a> </li><li style="width:5%;"> <a onclick="return confirm(\'删除 '.$row['id'].'?\');" href="'.site_url('manage/home/del/'.$row['id']).'">删除</a> </li></ul>
									<ul class="new_ul">
									<li style="width:20%;">时间</li>
                                    <li style="width:20%;">用户备注</li>
                                    <li style="width:8%;">预约状态</li>
                                    <li style="width:7%;">消费金额()</li>
                                    <li style="width:10%;">是否手术</li> 
                                    <li style="width:10%;">管理员备注</li> 
									<li style="width:10%;">派单机构</li
                                    <li style="width:10%";>派单状态</li>
                                    <div class="clear" style="clear:both;"></div></ul>
									<ul data-id="'.$row['id'].'" class="new_boarder">
									<li style="width:20%"><a target="_blank" href="'.$link.'">'.($row['yuyueDate']>1000?date('Y-m-d',$row['yuyueDate']):'').', '.$row['extraDay'].'</a></li>
                                	<li style="width:20%" class="Vertical08d">'.$row['remark'].'</li>
                                    <li style="width:8%">'.$ctstate[$row['state']].'</li>
                                	<li style="width:7%" class=" editamount" data-id="'.$row['id'].'"><a title="双击编辑">'.$row['amout'].'</a></li> 
									<li style="width:10%" class=" shoushu" data-id="'.$row['id'].'">'.$row['shoushu'].'</a></li>  
									<li style="width:10%" class="Vertical08d_admin">'.($row['admin_remark']==''?'<a href="'.site_url('counselor/admin_beizhu').'?height=120&width=320&dataid='.$row['id'].'" title="添加管理员备注" class="thickbox">添加备注</a>':$row['admin_remark']).'</li>
                                   <li style="width:10%">'.getuser($row['sn'],$this->db,$mifno).'</li> 
								   <li style="width:10%">'.($row['sendState']==0?'未派单':'已派单 '.$row['sendState'].' 次').'</li>
                                    
                                  
                                </ul><div class="clear" style="clear:both;"><div style="float:left;width:200px;">'.(!empty($manager)?'添加人:<a href="'.site_url('manage/users/detail/'.$manager[0]['id']).'">'.$manager[0]['alias'].'</a>':'').'   派单人:<a href="'.site_url('manage/users/detail/'.$mifno[0]['id']).'">'.$mifno[0]['alias'].'</a></div></div></div>';
									
								} 
								?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                            <div class="paging">
                                <div class="paging_right">
                            	 <?php echo $pagelink?>      </div>
                            </div>
                        </div>
                    </div><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script><script type="text/javascript">$(function(){
    
    var numTd = $(".editamount"); 
    numTd.dblclick(function() {     
        var tdObj = $(this);
        if (tdObj.children("input").length > 0) { 
            return false;
        }
        var text = tdObj.text();  
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/myueyueset', {"dataid":dataid,"amount":amount}, function(data) {  
   });}</script>
   <script type="text/javascript">
   var strLength = 10;
    $(function(){
        
        $(".Vertical08d").each(
                function(){
                	var oldStr = $(this).text();
                    var newStr = $(this).text().substring(0,strLength);
					if($(this).text().length>10){
                    $(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');
					}
                                   
                })
        $(".Vertical08d_admin").each(
                function(){ 
                	var oldStr = $(this).text();
                    var newStr = $(this).text().substring(0,strLength);
                    if($(this).has('a').length) {
                       
                    }else{if($(this).text().length>10){
                    	$(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');}
                        }
                    //$(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');
                                   
                })                
        //$(".Vertical08d").text().substring(0,strLength);
        //$(".Vertical08d").html('<div>'+newStr+'...</div>')
    })
	$(function(){
        $(".show_all").live("click",function(){
            var hidd = $(this).attr('oldd').substring(0,strLength);	
        	$(this).parent().html('<div>'+$(this).attr('oldd')+'</div>'+'<div class="hidden_all" style="color:red;float:left" hidd="'+hidd+'" oldd="'+$(this).attr('oldd')+'">隐藏</div>');
            })
        })
        $(".hidden_all").live("click",function(){
        	$(this).parent().html('<div>'+$(this).attr('hidd')+'...</div><div class="show_all" style="color:red;float:left" oldd="'+$(this).attr('oldd')+'">更多</div>');
        	})   
    $(function(){
		$(".cotuselect").citySelect({
    	prov:"<?php echo $this->input->get('province') ?>", 
    	city:"<?php echo $this->input->get('city') ?>",
		dist:"",
		nodata:"none",required:false, 
	});  
		 
      
  $(function() {
	  $( ".jiesuanDate" ).datepicker({ dateFormat: "yy-mm" }).val();
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  }); })       	         
  
</script>
   
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
