<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/ajaxfileupload.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('user/info') ?>">医院机构基本资料</a></li><li><a href="<?php echo site_url('user/hetong') ?>">合同扫描件</a></li><li class="selected"><!--<a href="<?php echo site_url('user/ablum') ?>">医院相册</a></li>--><li><a href="<?php echo site_url('user/changepass/3') ?>">修改密码</a></li>
                            </ul>
                        </div>
                        <div class="personal_information"> 
                        	<div class="photo_show">
                        	  <div class="photo_part1">
                            	<div class="photo_type">
                                	<h4>相册</h4>
                                    <ul>
                                    	<li><span class="btn upload">  
                                    	<form action="<?php echo site_url() ?>jquery/upjigouablum" method="post" enctype="multipart/form-data">
                                    
                                     <input type="file" id="uploadpicurl" class="picfile" multiple="" name="picfile">  </span> </li>
                                     <div>
                                         <input type="submit"  name=""/>
                                     </div>
                                     </form>
                                    
                                    	<li><input name="delete" id="delete" type="button" class="delete"></li>
                                        <li><label><input id="ckall" name="ckall" type="checkbox" value="1">点击全选</label></li>
                                    </ul><div id="loading">图片正在上传中...</div>
                                </div>
                                <div class="pohtos_list">
                                    <ul id="showpicuil">
                                    <?php  
									if(!empty($info)){ 
									    foreach($info as $row){
									        //print_r($row->savepath) ;
// 									 	    echo '<li> <img src="www.meilimei.com/'.$row->savepath.'" width="150" height="150"><p><input name="uppic" class="piclist" type="checkbox" value="'.$row->id.'"></p></li>';
									 	    echo '<li> <img src="'.$this->remote->show(str_replace('upload/','',$row->savepath)).'" width="150" height="150"><p><input name="uppic" class="piclist" type="checkbox" value="'.$row->id.'"></p></li>';
								       }  
									}  
									?> 
                                    </ul>
                                </div>
                                <div class="clear" style="clear:both;"></div>
                            </div>
                        </div>   
                        
                        </div>
                    </div>     <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 
        <script type="text/javascript">
		function ajaxFileUpload()
	{
		$("#loading")
		.ajaxStart(function(){
			$(this).show();
		})
		.ajaxComplete(function(){
			$(this).hide();
		});

		$.ajaxFileUpload
		(
			{
				url:'<?php echo site_url() ?>jquery/upjigouablum',
				secureuri:false,
				fileElementId:'uploadpicurl',
				dataType: 'JSON',
				data:{name:'picfile', id:'uploadpicurl'},
				
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							alert(data.error);
						}else
						{     $('#uploadpicurl').show(200); 
						 $("#showpicuil").append('<li><img  src="'+data.msg+'" width="150" height="150"><p><input name="uppic" class="piclist" type="checkbox" value="1"></p></li>'); 
						}
					}
				},
				error: function (data, status, e)
				{
					alert(e);
				}
			}
		)
		
		return false;

	}
$(function(){ 
  $("#ckall").click(function(){
	  if($(this).attr("checked")){
		  $(".piclist").each(function() {
                $(this).attr("checked", true);
         });
	  }else{
		   $(".piclist").each(function() {
                $(this).attr("checked", false);
         });
	  }
		  
  });
  $("#delete").click(function(){
	  var qry="";
	   $('input[name="uppic"]:checked').each(function(){ $(this).parent().parent().remove();
          qry+=$(this).val()+','; 
       }); 

	  $.get('<?php echo site_url() ?>jquery/deljigoupic.html', {"id":qry }, function(data) { 
          
	 })
 })
  $('#uploadpicurl').live("change",function(){  
  $(this).hide();
     ajaxFileUpload();  
   });   
}) 
 </script>