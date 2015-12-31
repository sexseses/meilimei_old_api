<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.imgareaselect.min.js"></script></head>
 <body><?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){ ?>
<script type="text/javascript">
function preview(img, selection) {   
	var scaleX = <?php echo $thumb_width;?> / selection.width; 
	var scaleY = <?php echo $thumb_height;?> / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 

$(function () { 
    var $edgesw = <?php echo ($current_large_image_width-$thumb_width)/2 ?>;
    var $edgesh = <?php echo ($current_large_image_height-$thumb_height)/2 ?>; 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val(); 
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val(); 
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			 $('#x1').val($edgesw);$('#y1').val($edgesh);$('#x2').val($edgesw+<?php echo $thumb_width;?>);$('#y2').val($edgesh+<?php echo $thumb_height;?>);$('#w').val(<?php echo $thumb_width;?>);$('#h').val(<?php echo $thumb_height;?>);
			return true;
		}else{
			return true;
		}
	}); 
   $('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', x1:$edgesw, y1:$edgesh, x2: $edgesw+<?php echo $thumb_width;?>, y2: $edgesh+<?php echo $thumb_height;?>, onSelectChange: preview });  
   $('#thumbnail + div > img').css({ 
		width: <?php echo $current_large_image_width;?> + 'px', 
		height: <?php echo $current_large_image_height;?> + 'px',
		marginLeft: '-' + $edgesw + 'px', 
		marginTop: '-' + $edgesh + 'px' 
	});

}); 
 

</script>
<?php }?> 
<?php 
if(strlen($error)>0){
	echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
}
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
	echo  $thumb_photo_exists;
	echo "<p><a href=\"".site_url('thumbplug')."\">重新上传</a></p><p><a href='#' onclick=\"top.tb_remove();\">完成</a></p> ";
	 
	 
	$this->session->set_userdata('random_key','');
	$this->session->set_userdata('user_file_ext','');
}else{
		if(strlen($large_photo_exists)>0){?> 
<div align="center" style="position:relative;width:660px;">
  <img src="<?php echo site_url().$upload_path.$large_image_name.$this->session->userdata('user_file_ext');?>?t=<?php echo time() ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
  <div style="border:1px #e5e5e5 solid; float:right; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
      <img src="<?php echo site_url().$upload_path.$large_image_name.$this->session->userdata('user_file_ext');?>?t=<?php echo time() ?>" style="position: relative;" alt="Thumbnail Preview" />
  </div>
  <div style="position:absolute; z-index:100;right:130px;top:260px;"><?php echo form_open_multipart("thumbplug",array('id' => 'thumbnail','name' => 'thumbnail'))?> 
      <input type="hidden" name="x1" value="" id="x1" />
      <input type="hidden" name="y1" value="" id="y1" />
      <input type="hidden" name="x2" value="" id="x2" />
      <input type="hidden" name="y2" value="" id="y2" />
      <input type="hidden" name="w" value="" id="w" />
      <input type="hidden" name="h" value="" id="h" />
      <input type="submit" name="upload_thumbnail" style="font-size:14px; width:90px; padding:3px 5px; margin-top:10px" value="保存图片" id="save_thumb" />
  </form></div>
</div>
 
<?php 	}else{ ?>
<h2>上传图片</h2><?php echo form_open_multipart("thumbplug",array('id' => 'photo','name' => 'photo'))?> 
<input type="file" name="image" size="30" /> <input type="submit" name="upload" value="上传" />
</form>
<?php }} ?> 
<div class="clear" style="clear:both;"></div> 
		</div> 

</body>
</html>               	