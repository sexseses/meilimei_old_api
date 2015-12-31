<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >
<script charset="utf-8" src="http://static.meilimei.com.cn/public/js/Validform_532_min.js"></script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/event'); ?>">特惠活动管理</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/event/add'); ?>">添加特惠活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart('manage/event/add',$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td width="200">活动名称 </td>
                        <td>
                        <input type="input" name="event_name"  id="tehuiid" size="30" datatype="s5-16" value="<?php echo $event_rs['event_name'] ?>" /><span style="color:red">*</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">开始时间-结束时间：</td>
                        <td><input type="input" name="begin" class="file" id="begin" size="28" value="<?php echo $event_rs['begin_time'] ?>"/>-<input type="input" name="end" class="file" id="end" size="28" value="<?php echo $event_rs['end_time'] ?>"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">活动简介</td>
                        <td><input type="input" name="subject" class="file"  size="50" value="<?php echo $event_rs['subject'] ?>"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">活动预付价格</td>
                        <td><input type="input" name="price" class="file"  size="20" value="<?php echo $event_rs['price'] ?>"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">活动发送短信文案</td>
                        <td><textarea name="sms"><?php echo $event_rs['sms'] ?></textarea><span style="color:red">*</span></td>
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
    $( "#begin" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    $( "#end" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    //$("#eventaddform").Validform();
});
</script>