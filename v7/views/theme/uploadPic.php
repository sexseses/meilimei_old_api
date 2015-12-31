<link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>public/css/uploadify.css" ><script type="text/javascript" src="<?php echo base_url() ?>public/js/jquery.uploadify.min.js"></script><?php echo form_open("user/uploadpic"); ?>
		<div id="queue"></div>
	 <input id="file_upload" class="inputbox" multiple="false" type="file" name="file_upload" />
	</form> 
	<script type="text/javascript">
		<?php $timestamp = time();?>
		$(function() {
			$('#file_upload').uploadify({
				'formData'     : {
					'timestamp' : '<?php echo $timestamp;?>',
					'token'     : '<?php echo md5('unique_salt' . $timestamp);?>',
					'sectoken'     : '<?php echo $this->wen_auth->get_user_id();?>',
					'wenhash':'<?php echo $this->security->get_csrf_hash() ?>',
				},
				'swf'      : '<?php echo base_url() ?>public/uploadify.swf',
				'uploader' : '<?php echo site_url("user/uploadpic") ?>',width         : 120, 
				'onUploadSuccess':function(file,data,response){
					var src = "<?php echo base_url().'images/users/'.$this->wen_auth->get_user_id().'/userpic.jpg?'.time()  ?>";$("#thumbPic").attr("src",src);  
                   original_tb_remove(); return false;	
				}
			});
		});  
	</script> 