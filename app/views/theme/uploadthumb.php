<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.imgareaselect.min.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                       <ul> <li class="selected"><a href="<?php echo site_url('user/info') ?>">修改资料</a></li>  </ul>
                        </div>
                        <div class="personal_information"><div style="padding:20px;"> <?php
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

$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
			alert("需要拖动区域设置图片截取范围");
			return false;
		}else{
			return true;
		}
	});
}); 

$(function () { 
	$('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $thumb_height/$thumb_width;?>', onSelectChange: preview }); 
});

</script>
<?php }?> 
<?php 
if(strlen($error)>0){
	echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
}
if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0){
	echo $large_photo_exists."&nbsp;".$thumb_photo_exists;
	echo "<p><a href=\"".site_url('thumb')."\">重新上传</a></p><p><a href=\"".site_url('user/info')."\">完成</a></p>";
	 
	 
	$this->session->set_userdata('random_key','');
	$this->session->set_userdata('user_file_ext','');
}else{
		if(strlen($large_photo_exists)>0){?>
		<h2>选择区域,截取图片</h2>
		<div align="center">
			<img  src="<?php echo site_url().$upload_path.$large_image_name.$this->session->userdata('user_file_ext');?>?t=<?php echo time() ?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
			<div style="border:1px #e5e5e5 solid; float:left; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
				<img src="<?php echo site_url().$upload_path.$large_image_name.$this->session->userdata('user_file_ext');?>?t=<?php echo time() ?>" style="position: relative;" alt="Thumbnail Preview" />
			</div>
			<br style="clear:both;"/><?php echo form_open_multipart("thumb",array('id' => 'thumbnail','name' => 'thumbnail'))?> 
				<input type="hidden" name="x1" value="" id="x1" />
				<input type="hidden" name="y1" value="" id="y1" />
				<input type="hidden" name="x2" value="" id="x2" />
				<input type="hidden" name="y2" value="" id="y2" />
				<input type="hidden" name="w" value="" id="w" />
				<input type="hidden" name="h" value="" id="h" />
				<input type="submit" name="upload_thumbnail" style="font-size:14px; width:90px; padding:3px 5px;" value="保存图片" id="save_thumb" />
			</form>
		</div>
 
	<?php 	}else{ ?>
	<h2>上传图片</h2><?php echo form_open_multipart("thumb",array('id' => 'photo','name' => 'photo'))?> 
  <input type="file" name="image" size="30" /> <input type="submit" name="upload" value="上传" />
	</form>
<?php }} ?> </div>
                        </div>
                    </div> 
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 
                	