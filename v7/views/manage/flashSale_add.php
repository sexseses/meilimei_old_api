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
        window.editor = K.create('#context', {
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
                    <li class="on"><a href="<?php echo base_url('manage/flashSale')?>">闪购管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/add')?>">添加闪购活动</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart('manage/flashSale/add',$attributes,$hidden); ?>
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td width="100">标题：</td>
                        <td><input type="input" name="title"  id="" size="28" /><span style="color:red">*</span></td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">活动内容：</td>
                        <td>
                            <textarea name="context" id="context" rows="20" cols="50"></textarea>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">城市：</td>
                        <td>
                            <input type="input" name="city"  id="" size="28" />
                        </td>
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
                        <td width="100">折扣力度：</td>
                        <td>
                            <input type="input" name="discount"  id="discount" size="28" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">排序：</td>
                        <td>
                            <input type="input" name="level"  id="level" size="28" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">虚拟购买数：</td>
                        <td>
                            <input type="input" name="vbuy"  id="vbuy" size="28" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">分享图片</td>
                        <td>
                            <input name="share_pic" type="file" size="45"/>
                            
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">分享标题</td>
                        <td>
                            <input type="input" name="share_title"  id="share_title" size="28" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="100">活动类型：</td>
                        <td>
                            <input type="radio" name="type" value="1" />机构
                            <input type="radio" name="type" value="2" />项目
                            <input type="radio" name="type" value="3" />医师
                            <input type="input" name="type_id" id="type_id" size="28" />
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
