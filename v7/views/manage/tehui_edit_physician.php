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
                    <li ><a href="<?php echo base_url('manage/tehui')?>">特惠活动管理</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/tehui/tehui_add')?>">添加特惠活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart("manage/tehui/tehui_edit_physician/{$event_id}",$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td style="width=10px"></td>
                        <td>
                            <?php if(!empty($results)){ ?>
                                <?php foreach ($results as $key => $value) { ?>
                                <input <?php if(in_array($value['user_id'],$physician)){echo 'checked';};?> datatype="*" errormsg="请选择医师" nullmsg="请选择医师"  type="checkbox" name="physician[]" id="<?php echo $value['user_id'];?>" value="<?php echo $value['user_id'];?>" /><label for="<?php echo $value['user_id'];?>"><?php echo $value['alias'];?></label>
                                <?php echo '<br/>'; } ?>    
                            <?php 
                                }else{
                                    echo "该机构没有医师！";
                                }
                            ?>
                        </td>
                    </tr>
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