<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="#">客户记录</a></li><li style="float:right;"></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form">
								<div class="manage_search">
								<form method="post"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                            	<ul>  
                                    <li class="f5 cotuselect">位置
                               <select class="prov" name="province" id="province"> 
                                </select> <select class="city" name="city" id="city">  </select>
                                </li> 
                                    <li>医院 <select class="hostipt" name="hostipt" id="hostipt"> <option>未获取</option>
                                </select></li> 
                                    <li><input name="getyiyuan" type="button" value="搜索" class="search">(先获取医院并选择)</li>
                                    <Li style="display:block;width:100%;height:50px;"><label>备注</label><textarea style="height:50px;width:500px" name="remarks"></textarea></Li>
                                   <li><label>项目</label><input type="text" value="" name="items"/> </li>
                                   <li><input name="paidan" type="submit" value="派单" style="background:#008000;color:#fff;" class="paidan"></li>
                                </ul>
                                </form>
                            	</div>
                            	 
                                 <?php  
                                function DS($res,$city){
									$html = '';
									foreach($res as $r){
                                      $r['city']==$city&&$html .='<option value="'.$r['userid'].'">'.$r['name'].'</option>';
                                    }
									return $html;
								} ?>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script> 
   <script type="text/javascript">
   var strLength = 10;
    $(function(){
        
        $(".Vertical08d").each(
                function(){
                	var oldStr = $(this).text();
                    var newStr = $(this).text().substring(0,strLength);
                    $(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');
   
                                   
                })
        $(".Vertical08d_admin").each(
                function(){ 
                	var oldStr = $(this).text();
                    var newStr = $(this).text().substring(0,strLength);
                    if($(this).has('a').length) {
                       
                    }else{
                    	$(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');
                        }
                    //$(this).html('<div>'+newStr+'...</div><div class="show_all" style="color:red;float:left" oldd="'+oldStr+'">更多</div>');
                                   
                })                
        //$(".Vertical08d").text().substring(0,strLength);
        //$(".Vertical08d").html('<div>'+newStr+'...</div>')
    })
    $(function(){
		$(".cotuselect").citySelect({
    	prov:"", 
    	city:"",
		dist:"",
		nodata:"none",required:false, 
	}); 
		
		$(".search").click(function(){ 
		var ac = $("#hostipt") ; 
			$.ajax({
type: 'GET',
url: '/manage/getdata/yiyuan',
async: false,
data: { city: $("#city").val(),province: $("#province").val()},
success:function(data){ ac.empty(); ac.append(data);
} 
});
			
		})
      
  $(function() {
    $( ".datepicker" ).datepicker();
  });        	         
	})
</script>
   
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
