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
            <?php echo form_open_multipart("manage/tehui/tehui_edit_items/{$id}",$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td style="width=10px"></td>
                        <td>
                            <?php foreach($item_data as $item) {?>
                                <input datatype="*" errormsg="请选择项目" nullmsg="请选择项目"  type="checkbox" name="items[]" id=" " value="<?php echo $item['name'];?>"
                                <?php 
                                if(in_array($item['name'], unserialize($row['items']))) { 
                                    echo "checked='checked'";}
                            ?>/>
                                <label><?php echo $item['name'];?></label>
                            <?php } ?>
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