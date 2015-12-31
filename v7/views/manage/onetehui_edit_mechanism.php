<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >
<script charset="utf-8" src="http://static.meilimei.com.cn/public/js/Validform_532_min.js"></script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li class="on"><a href="<?php echo base_url('manage/onetehui')?>">一元活动管理</a></li>
                    <li><a href="<?php echo base_url('manage/onetehui/onetehui_add')?>">添加活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');

            ?>
            <?php echo form_open_multipart("manage/onetehui/onetehui_edit_mechanism/{$event_id}",$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <?php foreach ($mechanism_rs as $key => $value) { ?>
                        <tr>
                            <td style="width=10px"></td>
                            <td>
                                <?php  
                                    foreach ($value as $k => $v) {
                                ?>
                                    <input datatype="*" errormsg="请选择机构" nullmsg="请选择机构"  type="radio" name="mechanism" id="<?php echo $v['id'];?>" value="<?php echo $v['id'];?>" edit/><label for="<?php echo $v['id'];?>"><?php echo $v['name'];?></label>
                                <?php
                                    echo '<br/>';
                                    }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="保存" style="padding:2px 5px;margin:10px 0;"></td>
                    </tr>
                </table>
        	</div>
            </form>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
<script>
$(function() { 
    $("#eventaddform").Validform();
});
</script>