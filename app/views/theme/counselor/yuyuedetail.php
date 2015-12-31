<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('counselor/yuyue') ?>">客户记录</a>详细</li>
                            	 
                            </ul>
                        </div><style type="text/css">dd{display:block;margin:10px auto;line-height:30px;height:30px;} dd label{display:inline-block;width:100px;}</style>
                        <div class="manage_yuyue" >
                        	<div class="yuyue_form">
                      <?php  $ctstate = array('待联系','已联系','无法联系','已到院未手术','已到院手术');
                		 
								 ?> 
                                 <form method="post" enctype="multipart/form-data">
							<dl><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                               <dd><label>ID</label> <?php echo $res[0]['id'] ?></dd>
                               <dd><label>SN</label> <?php echo $res[0]['sn'] ?></dd>
                               <dd><label>姓名</label> <?php echo $res[0]['name'] ?></dd>
                               <dd><label>手机</label> <?php echo $res[0]['phone'] ?></dd> 
                               <dd><label>消费金额</label> <input name="amout" type="text" value="<?php echo $res[0]['amout'] ?>" /></dd> 
                               <dd><label>是否手术</label> <?php echo $res[0]['shoushu'] ?></dd> 
                               <dd><label>是否重单</label> <select class="chongdan" name="chongdan"><option value="0" <?php echo $res[0]['chongdan']==0?'selected="selected"':'' ?>>否</option><option value="1" <?php echo $res[0]['chongdan']==1?'selected="selected"':'' ?>>是</option></select></dd> 
                               <?php if($res[0]['chongdan']==1){echo '<dd style="height:300px"><img width="300" height="300" src="'.site_url().'upload/'.$res[0]['linkpic'].'"/></dd>'; }?>
                               <dd id="upchongd" style="<?php echo $res[0]['chongdan']==0?'display:none;':'' ?>"><label>上传重单图片</label><input type="file" name="picture" /></dd> 
                               <dd style="line-height:160%;"><label style="line-height:160%;">用户备注</label> <?php echo $res[0]['remark'] ?> </dd>
                               <dd style="height:20px"></dd>
                               <dd style="line-height:160%;"><label style="line-height:160%;">客服备注</label> <?php echo $res[0]['sendremark'] ?> </dd>
                               
                               <dd style="height:20px"></dd>
                               <dd><label>预约时间</label> <?php echo date('Y-m-d H:i:s',$res[0]['yuyueDate']) ?></dd>
                              
                              <dd><label>联系状态</label><select name="contactState"><option value="0" <?php echo $res[0]['contactState']==0?'selected="selected"':'' ?>>待联系</option><option <?php echo $res[0]['contactState']==1?'selected="selected"':'' ?> value="1">已联系</option> <option <?php echo $res[0]['contactState']==2?'selected="selected"':'' ?> value="2">无法联系</option> <option <?php echo $res[0]['contactState']==3?'selected="selected"':'' ?> value="3">已到院未手术</option> <option <?php echo $res[0]['contactState']==4?'selected="selected"':'' ?> value="4">已到院手术</option> </select></dd> 
                               <dd><label>下次联系时间</label><input type="text" class="datepicker" name="nextdate" value="<?php echo $res[0]['cnextdate']?date('Y-m-d',$res[0]['cnextdate']):date('Y-m-d') ?>" /></dd> 
                               <dd><button style="height:30px;  margin-right:10px;width:80px;" type="submit">修改</button> <a href="<?php echo site_url('counselor/yuyue') ?>" style="height:30px;width:80px;" type="button">关闭</a>  </dd>   
                            </dl></form><button id="showbeizhu" style="font-size:14px;">打开备注</button>
                                 <div class="clear" style="clear:both;"></div>      
<iframe style="display:none" id="beizhutaok" frameborder="0" width="100%;" height="300px"  scrolling="auto" src="http://www.meilimei.com/counselor/paidantalk/<?php echo $param ?>.html"></iframe>
                            </div> 
                        </div>
                    </div> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> <script type="text/javascript">$(function(){
    $("#showbeizhu").click(function(){
		$("#beizhutaok").toggle(300);
	})
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
      $(function() {$(".chongdan").change(function(){ if($(this).val()==1){$("#upchongd").show()}else{$("#upchongd").hide()}});
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