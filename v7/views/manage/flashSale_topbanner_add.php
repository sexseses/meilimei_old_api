<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >
<script charset="utf-8" src="http://static.meilimei.com.cn/public/js/Validform_532_min.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
 
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li class="on"><a href="<?php echo base_url('manage/flashSale')?>">闪购管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/add')?>">添加闪购活动</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/IndexTopBanner')?>">首页置顶活动</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/IndexTopBanner_add')?>">添加置顶活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart('manage/flashSale/IndexTopBanner_add',$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td width="100">特惠编号：</td>
                        <td><input type="input" name="tehui_id"  id="" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">活动时间：</td>
                        <td>
                            <input type="input" name="begin"  id="begin" size="28" /> - <input type="input" name="end"  id="end" size="28" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                     <tr>
                        <td>
                            <input name="banner" type="file" size="45"/>
                            <input type="hidden" name="pictype" value="1">
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
$( "#begin" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
$( "#end" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
</script>