<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('counselor/yuyue') ?>">客户记录</a>(您当前有<?php echo $jifen ?>积分)</li>
                            	 
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                       <div class="manage_search">
								<form  method="get" action="<?php echo site_url('counselor/search') ?>">
                            	<ul>  
                                    <li>会员ID<input name="ID" type="text" value="<?php  echo $this->input->get('ID'); ?>"></li>
                                  
                                    <li>姓名<input name="name" value="<?php  echo $this->input->get('name'); ?>" type="text"></li> 
                                    <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $this->input->get('yuyueDateStart'); ?>" class="datepicker"></li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $this->input->get('yuyueDateEnd'); ?>" class="datepicker"></li> <li>手术状态<select name="ssstate"><option ></option><option <?php   echo $this->input->get('ssstate')=='否'?'selected="selected"':'';   ?> value="否">否</option><option value="是" <?php if($this->input->get('ssstate')) { echo $this->input->get('ssstate')=='是'?'selected="selected"':''; } ?>>是</option></select></li>
                                    <li>手机<input name="phone" type="text"></li>
 <Li>今日需联系用户<input <?php if($this->input->get('tnc')) { echo 'checked="checked"'; } ?> style="width:20px" name="tnc" type="checkbox" /></Li> 
                                    <li><a href="<?php echo site_url('counselor/search') ?>?ac=today">今日派单</a></li> 
                                    <li><a href="<?php echo site_url('counselor/search') ?>?ac=month">本月派单</a></li>
                                    <li><a href="<?php echo site_url('counselor/search') ?>?ac=newms">新留言</a></li>
                                    <li><input name="" type="submit" value="搜索" class="search"></li>
                                </ul>
                                </form>
                            	</div>
                        	<div class="yuyue_form">
                            <ul class="new_ul">
                                        <li style="width:10%;">ID</li>
                                        <li style="width:15%;">姓名</li>
                                        <li style="width:15%;">手机</li>
										<li style="width:15%;">派单时间</li>
                                      
	                                    <li style="width:15%;">联系状态</li>
	                                    <li style="width:15%;">消费金额()</li> 
	                                    <li style="width:10%; ">操作</li>
                                    </ul>
                      <?php 
								$ctstate = array('待联系','已联系','无法联系','已到院未手术','已到院手术');
                				$wlink = $row['userby']!=0?'<a href="'.site_url('manage/users/detail/'.$row['userby']).'">'.$row['name'].'<a>':$row['name'];
								foreach($data as $row)
								{?>  
									 
                                    <ul style="background-color:<?php echo $row['is_view']==1?'#fff':'#cec'?>;list-style-type:none; overflow:hidden;" data-id="<?php echo $row['Yid']?>">
                                    <li class="Vertical01" style="width:10%;"><?php echo $row['userby']?></li>
                                    <li class="Vertical01" style="width:15%;"><?php echo $row['name']?></li>
                                    <li class="Vertical01" style="width:15%;"><?php echo $row['phone']?></li> 
                                	<li class="Vertical01" style="width:15%;"><?php echo date('Y-m-d H:i:s',$row['cdate'])?></li> 
                                    <li style="width:15%;" class="Vertical08 contact" data-id="<?php echo $row['sn']?>"> <?php echo $ctstate[$row['contactState']] ?> </li>
                                    <li style="width:15%;" class="Vertical09 editamount" data-id="<?php echo $row['sn']?>"><?php echo $row['amout']?></li>
                                    
                                     <li class="<?php echo $row['is_view']?'is_new':'' ?>" style="width:10%;float:right;"><a class="<?php echo site_url('counselor/danview/'.$row['id']) ?>" href="<?php echo site_url('counselor/danview/'.$row['id'])?>">查看</a></li>
                                     
                                     
                                </ul> <div class="clear" style="clear:both;"></div>                                
									
								<?php }?>
								

                                <div class="clear" style="clear:both;"></div>

                            </div>
                            <div class="paging">
                                <div class="paging_right">
                            	<ul>
                                    <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                                    <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                </ul>
                                <h5>第<?php echo $offset ?>-<?php echo $offset+count($data)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                            </div>
                            </div>
                        </div>
                    </div> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> <script type="text/javascript">$(function(){
    
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
}); function sendamount(amount,dataid){$.get('<?php echo site_url() ?>jquery/yueyueset', {"dataid":dataid,"amount":amount}, function(data) {  

   });}</script>
 
   
   <script type="text/javascript">
   $(".addRemark").click(function(){
	   //$('<div><a href="'.site_url('counselor/beizhu').'?height=120&width=320&dataid='.$row['id'].'" title="添加备注" class="thickbox">备注</a></div>').insertBefore($(this));
	   
	   });
   $(function(){
        $("#addRec").click(function(e){
       
             e.preventDefault();
             $("#dialog").dialog({
         	    bgiframe: true,
        	    resizable: false,
        	    height:140,
        	    modal: true,
        	    overlay: {
        	        backgroundColor: '#000',
        	        opacity: 0.5
        	    },
        	    buttons: {
        	        '提交': function() {
        	            $(".beizhu").submit();
        	        },
        	        Cancel: function() {
        	            $(this).dialog('close');
        	        }
        	    }
        	});
            })
	   })
    $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });   
   </script>
                    <div class="clear" style="clear:both;"></div>
                    <div id="dialog" title="Basic dialog" style="display:none">
                        <form action="<?php echo site_url('counselor/submitNewBeizhu')?>" method="post" class="beizhu">
                            <input type="text" name="comments" >
                            <input type="hidden" id="<?php echo $result['paramname']?>" name="<?php echo $this->security->csrf_token_name?>" value="<?php echo $this->security->csrf_hash?>" /> 
                        </form>
                    </div>
                </div>
            </div>
		</div> 