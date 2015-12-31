<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/autosuggest_inquisitor.css" >
<div class="page_content933"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.validate.js"></script>
<div class="institutions_info new_institutions_info">
	<?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
        <div class="personal_information"><?php
		 $attributes = array('id' => 'usersubmit'); 
		 echo form_open_multipart("manage/tehuiad/update/$id",$attributes); ?>
        	<ul>
                <li>
                	<div class="k1"> <span></span>url：</div>
                    <div class="k2"><p><input type="text" name="url" value="<?php echo isset($url)?$url:'' ?>" /> </p></div>
                </li>
               <li>
                	<div class="k1"><span></span> 图片：</div>
                    <div class="k2">				    
                    <input type="file" name="file">
					</div>
                </li>
                <div class="clear" style="clear:both;"></div>
                <div class="k1"> </div>
                <div class="k2"> <input style="font-size:14px; width:90px; padding:3px 5px;"  type="button" onclick="parent.location.href='<?php echo site_url('manage/tehuibanner')?>'" value="关闭" />
                <input id="normalsubmit" onclick="return false" style="font-size:14px; width:90px; padding:3px 5px;" type="submit" value="保存" />  </div>
            </ul> <?php echo form_close(); ?>
        </div>
    </div>
<script>
$('#normalsubmit').bind('click',function(){  
	  $("#submittype").val(1);
	  $('#usersubmit').submit();
	});
</script>         