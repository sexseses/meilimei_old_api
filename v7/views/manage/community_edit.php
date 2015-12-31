<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >
<script charset="utf-8" src="http://static.meilimei.com.cn/public/js/Validform_532_min.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function (K) {
        window.editor = K.create('#event_context', {
                        items:[
        'source', '|', 'undo', 'redo', '|', 'preview', 
        'plainpaste',  'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull',  'clearhtml', 'selectall', 'bold','italic', 'underline', 'removeformat', 'image'
        ]}); 
        //window.editor = K.create('#context',{width:'700px'});
    });
</script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/community')?>">社区活动管理</a></li>
                    <li ><a href="<?php echo base_url('manage/community/add')?>">添加社区活动</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/community/edit')?>">修改社区活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'edit'); 
                $attributes = array('id' => 'eventeditform');
            ?>
            <?php echo form_open_multipart('manage/community/edit',$attributes,$hidden); ?>
            <input type="hidden" name="id" value="<?php echo $id;?>">
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td width="100">标题：</td>
                            <td><input type="input" name="event_title"  id="event_title" size="28"  value="<?php echo $community_rs['event_title']; ?>"/><span style="color:red">*</span></td>
                        </tr>
                    <tr height="20">
                         
                    </tr>
                     <tr>
                        <td width="100">活动类型：</td>
                        <td>
                            <input type="radio" name="event_type" value="baoming" <?php if($community_rs['event_type'] == "baoming"){ ?> checked="checked" <?php } ?>/>报名
                            <input type="radio" name="event_type" value="fatie" <?php if($community_rs['event_type'] == "fatie"){ ?> checked="checked" <?php } ?>/>发帖
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">美豆数量：</td>
                        <td>
                            <input type="input" name="event_score"  id="" size="28"  value="<?php echo $community_rs['event_score']; ?>"/>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">活动时间：</td>
                        <td>
                            <input type="input" name="begin_time"  id="begin_time" size="28" value="<?php echo date('Y-m-d',$community_rs['begin_time']); ?>"/> - <input type="input" name="end_time"  id="end_time" size="28" value="<?php echo date('Y-m-d',$community_rs['end_time']); ?>"/>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">活动内容：</td>
                        <td>
                            <textarea name="event_context" id="event_context" rows="20" cols="50"><?php echo $community_rs['event_context']; ?></textarea>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">分享图片：</td>
                        <td>
                            <img style="max-width:300px;" src="<?php echo $this->remote->show($community_rs['share_pic']) ?>">
                            <input type="file" name="new_share_pic" id="new_share_pic">
                            <input type="hidden" name="share_pic" id="share_pic" value="<?php echo $community_rs['share_pic']; ?>">
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">分享标题：</td>
                        <td>
                            <input type="input" name="share_title"  id="share_title" size="28" value="<?php echo $community_rs['share_title']; ?>">
                        </td>
                    </tr>
                    <tr height="20">
                         
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
$( "#begin_time" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
$( "#end_time" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
</script>
