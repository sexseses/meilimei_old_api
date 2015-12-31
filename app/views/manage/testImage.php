<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >


<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <?php 
                $hidden = array('act' => 'upimage'); 
                $attributes = array('id' => 'uploadform');
            ?>
            <?php echo form_open_multipart('manage/testImage/upload',$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td>
                            <input name="pic" type="file" size="45"/>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="保存" style="padding:2px 5px;margin:10px 0;"></td>
                    </tr>
                </table>
        	</div>
            </form>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
