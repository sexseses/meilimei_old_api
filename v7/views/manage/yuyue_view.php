<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>   
  <script>
  $(function() {
    var name = $( "#dayname" ), shouShuCompany = $("#shouShuCompany"),
      allFields = $( [] ).add( name ).add( shouShuCompany ) ,
      tips = $( ".validateTips" );
 
    function updateTips( t ) {
      tips
        .text( t )
        .addClass( "ui-state-highlight" );
      setTimeout(function() {
        tips.removeClass( "ui-state-highlight", 1500 );
      }, 500 );
    }
 
    function checkLength( o, n, min, max ) {
      if ( o.val().length > max || o.val().length < min ) {
        o.addClass( "ui-state-error" );
        updateTips( "Length of " + n + " must be between " +
          min + " and " + max + "." );
        return false;
      } else {
        return true;
      }
    }
 
    function checkRegexp( o, regexp, n ) {
      if ( !( regexp.test( o.val() ) ) ) {
        o.addClass( "ui-state-error" );
        updateTips( n );
        return false;
      } else {
        return true;
      }
    }
 
    $( "#dialog-form" ).dialog({
      autoOpen: false,
      height: 300,
      width: 350,
      modal: true,
      buttons: {
        "添加": function() {
          var bValid = true;
          allFields.removeClass( "ui-state-error" );
 
          bValid = bValid && checkLength( name, "username", 3, 16 ); 
         
 
          if ( bValid ) {var senddata  = name.val(),comdata  = shouShuCompany.val(),comtxt=shouShuCompany.text(); 
            $( "#moredates" ).append( "<li>" +$("#shouShuCompany option:selected").text() +" " + senddata +"</li>" ); 
            $( this ).dialog( "close" );
			$.get("../../../manage/home/ajaxAdd/<?php echo $res[0]['sn'] ?>",{'addval':senddata,'comdata':comdata,'comtxt':comtxt},function(result){ 
            });
          }
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      },
      close: function() {
        allFields.val( "" ).removeClass( "ui-state-error" );
      }
    });
 
    $( "#create-user" )
      .button()
      .click(function() {
        $( "#dialog-form" ).dialog( "open" );
      });
  });
  </script><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="#">客户记录</a></li><li style="float:right;"></li>
                            </ul>
                        </div><style type="text/css">dd{display:block;margin:10px auto;line-height:30px;height:30px;} dd label{display:inline-block;width:100px;}</style>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form"><dl><form method="post"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
							
                               <dd><label>ID</label> <?php echo $res[0]['id'] ?></dd>
                               <dd><label>SN</label> <?php echo $res[0]['sn'] ?></dd>
                               <dd><label>姓名</label> <input type="text" value="<?php echo $res[0]['name']?$res[0]['name']:$res[0]['alias'] ?>" name="uname" /></dd>
                               <dd><label>手机</label> <input type="text" value="<?php echo $res[0]['phone']?$res[0]['phone']:$res[0]['uphone'] ?>" name="phone" /></dd>
                               <dd><label>位置</label><input type="text" value="<?php echo $res[0]['city']==''?$res[0]['city2']:$res[0]['city'] ?>" name="city" /></dd>
                               <dd><label>预约状态</label><select name="state"><option value="0" <?php echo $res[0]['state']==0?'selected="selected"':'' ?>>待确认</option><option <?php echo $res[0]['state']==1?'selected="selected"':'' ?> value="1">已确认</option>  <option <?php echo $res[0]['state']==2?'selected="selected"':'' ?> value="2">未通过</option> </select></dd> 
                               <dd><label>消费金额</label> <input name="amout" type="text" value="<?php echo $res[0]['amout'] ?>" /></dd> 
                               <dd><label>是否手术</label> <select name="shoushu"><option <?php echo $res[0]['shoushu']=='是'?'selected="selected"':'' ?>>是</option><option <?php echo $res[0]['shoushu']=='否'?'selected="selected"':'' ?>>否</option></select></dd> 
                                <dd><label>客户状态</label> <select name="ystate">
                                <option value="1" <?php echo $res[0]['ystate']==1?'selected="selected"':'' ?>>无效客户</option>
                                <option value="2" <?php echo $res[0]['ystate']==2?'selected="selected"':'' ?>>普通客户</option>
                                <option value="3" <?php echo $res[0]['ystate']==3?'selected="selected"':'' ?>>重点客户</option>
                                <option value="4" <?php echo $res[0]['ystate']==4?'selected="selected"':'' ?>>已手术</option>
                                <option value="5" <?php echo $res[0]['ystate']==5?'selected="selected"':'' ?>>需核实</option>
                                </select></dd> 
                              <dd style="height:60px;"><label>客服备注</label> <textarea name="cremark" style="display:inline-block" cols="55" rows="2"><?php echo $res[0]['cremark'] ?></textarea></dd>
                               <dd style="height:60px;"><label>用户备注</label> <textarea name="remark" style="display:inline-block" cols="55" rows="2"><?php echo $res[0]['remark'] ?></textarea></dd>               
                               <dd style="height:60px;"><label>管理员备注</label> <textarea name="admin_remark" style="display:inline-block" cols="55" rows="2"><?php echo $res[0]['admin_remark'] ?></textarea></dd>
                                <dd><label>重单</label> 
                                <?php 
								foreach($chongdan as $r){
									echo ' <input type="checkbox" '.($r['chongdan']?'checked="checked"':'').' name="chongdan[]" value="'.$r['id'].'">'.$r['alias'];
								}
								?>
                                </dd>
                               <dd><label>下次联系时间</label><input type="text" class="datepicker" name="nextdate" value="<?php echo date('Y-m-d',($res[0]['nextdate']<10?time():$res[0]['nextdate'])) ?>"></dd>        
                             
                               <dd><label>预约时间</label> <?php echo ($res[0]['yuyueDate']>10000?date('Y-m-d',$res[0]['yuyueDate']):'').$res[0]['extraDay'] ?></dd>
                               <dd><label>操作时间</label> <?php echo date('Y-m-d H:i:s',$res[0]['cdate'])  ?></dd>
                               <dd><button style="height:30px;  margin-right:10px;width:80px;" name="save" type="submit">修改</button> <a href="<?php echo site_url($this->session->userdata('history_url')) ?>" style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" type="button">关闭</a>  <a style="height:30px;width:80px;background:#E8E8E8;padding:3px 8px;border:solid 1px #D8D8D8" href="<?php echo site_url('manage/home/paidan/'.$params) ?>" type="button">派单</a> <button name="savepaidan" style="height:30px;  margin-right:10px;width:80px;" type="submit" value="sendpaidan">保存派单</button></dd> 
                               </form>
                             <dd><label>结算月</label><button id="create-user" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only ui-state-hover" role="button" aria-disabled="false"><span class="ui-button-text">添加</span></button>
                             <style type="text/css">#moredates li{display:block;width:100%}</style>
                             <ul id="moredates">
                              <?php
							   $this->db->where('sn',$res[0]['sn']);
							   $tmp = $this->db->get('yueyueLog')->result_array();
							   foreach($tmp as $r){
								  echo '<li>'.$r['company'].'  '.date('Y年m月',$r['jiesuan']).'</li>';   
							   }
							  ?>
                             </ul>
                              </dd>  </dl>
                                <div class="clear" style="clear:both;"></div>
                            </div> 
                        </div>
                    </div><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> 
   <script type="text/javascript">
   var strLength = 10; 
  $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
	 $( ".datepickers" ).datepicker({ dateFormat: "yy-mm" }).val();
  });   
</script>  <div id="dialog-form" title="添加结算月"> 
  <form>  
   <fieldset><dl><dd>
    <label for="dayname">选择日期</label>
    <input type="text" name="name" id="dayname" class="text datepickers ui-widget-content ui-corner-all">
   </dd><dd> <label for="shouShuCompany">手术医院</label>
    <select name="shouShuCompany" id="shouShuCompany">
    <option value="" ></option>
    <?php 
	 foreach($chongdan as $r){
	 echo ' <option '.($r['chongdan']?'selected="selected"':'').' value="'.$r['uid'].'">'.$r['alias'].'</option>';
	 }
	 	?>
    </select> 
    </dd></dl>
  </fieldset>
  </form>
</div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
