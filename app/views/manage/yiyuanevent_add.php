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
        window.editor = K.create('#content', {
                        items : [
        'source', '|', 'undo', 'redo', '|', 'preview', 'template', 'code', 'cut', 'copy', 'paste',
        'plainpaste', 'wordpaste', '|', 'justifyleft', 'justifycenter', 'justifyright',
        'justifyfull', 'insertorderedlist', 'insertunorderedlist', 'indent', 'outdent', 'subscript',
        'superscript', 'clearhtml', 'quickformat', 'selectall', '|', 'fullscreen', '/',
        'formatblock', 'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold',
        'italic', 'underline', 'strikethrough', 'lineheight', 'removeformat', '|', 
         'media', 'insertfile', 'table', 'hr',   'baidumap', 'pagebreak',
        'anchor', 'link', 'unlink' 
        ]
                }); 
        window.editor = K.create('#event_context',{width:'700px'});
    });
</script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/yiyuanevent')?>">一元活动管理</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/yiyuanevent/event_add')?>">添加活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart('manage/yiyuanevent/event_add',$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td>地区： </td>
                        <td>
                            <?php foreach($city as $item) {?>
                            <input datatype="*" errormsg="请选择地区" nullmsg="请选择地区"  type="checkbox" name="city[]" id="c<?php echo $item['id'];?>" value="<?php echo $item['city'];?>" />
                            <label for="c<?php echo $item['id'];?>">
                            <?php echo $item['city'];?></label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="100">活动名称： </td>
                        <td><input type="input" name="event_name"  id="event_name" size="32" datatype="s5-16" nullmsg = "活动名称不能为空!"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">活动时间：</td>
                        <td><input type="text" name="begin_time" id="begin_time" datatype="*" nullmsg = "活动开始时间不能为空!"/>-<input type="text" name="end_time"  id="end_time" datatype="*" nullmsg = "活动结束时间不能为空!"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">添加banner：</td>
                        <td><input type="file" name="banner_path" class="file" id="banner_path" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">特惠地址：</td>
                        <td><input type="input" name="tehuiurl" class="file" id="tehuiurl" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">特惠id：</td>
                        <td><input type="input" name="tehuiid" class="file" id="tehuiurl" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">banner标题：</td>
                        <td><input type="input" name="banner_title"  id="" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">活动页面保存：</td>
                        <td><input type="checkbox" checked="checked" name="save_page">保存</td>
                    </tr>
                    <tr>
                        <td width="100">活动封面：</td>
                        <td><input type="file" name="event_pic" class="file" id="banner_path" /><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td width="100">活动内容：</td>
                        <td>
                            <textarea name="event_context" id="event_context" rows="20" cols="50"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td width="100">奖品规则：</td>
                        <td>
                            <textarea name="gift_rule" id="gift_rule" rows="10" cols="50"></textarea><span style="color:red">*</span>
                        </td>
                    </tr>
                    <tr>
                        <td width="100">虚拟点赞数：</td>
                        <td>
                            <input type="input" name="virtual_support"  id="" size="28" /><span style="color:red">*</span>
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
    $( "#begin_time" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    $( "#end_time" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
    //$("#eventaddform").Validform();
});
</script>