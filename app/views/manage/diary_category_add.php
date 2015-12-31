<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" > 
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
        window.editor = K.create('#description',{width:'700px'});
    });
</script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li><a href="<?php echo site_url('manage/diary'); ?>">美人记管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/add'); ?>">添加</a></li>
                    <li><a href="<?php echo site_url('manage/diary/category'); ?>">目录管理</a></li>
                    <li  class="on"><a href="<?php echo site_url('manage/diary/addcategory'); ?>">添加目录</a></li>
                    <li><a href="<?php echo site_url('manage/diary/comments'); ?>">评论管理</a></li>
                    <li><a href="<?php echo site_url('manage/diary/check'); ?>">待审核</a></li>
                    <li><a href="<?php echo site_url('manage/diary/total'); ?>">统计</a></li>
                </ul>
            </div><style type="text/css">.manage_yuyue label{display:inline-block; padding:2px 20px 2px 0;font-size:12px;}</style>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div><?php echo form_open_multipart('manage/diary/addcategory') ?>

                        <table>
                            <tr>
                                <td  style="width:80px;">标题: </td>
                                <td><input type="text" name="title"/></td>
                            </tr>
                            <tr>
                                <td>发布的用户</td>
                                <td><input type="text" value="" name="user_id" id="user_id" /><input type="hidden" id="uid" name="uid" value="" /></td>
                            </tr>
                            <tr>
                                <td>目录描述</td>

                                <td><textarea name="desc"></textarea> </td>
                            </tr>
                            <tr>
                                <td>术前图片: </td>
                                <td><input type="file" name="noteCategoryPic" /></td>
                            </tr>
                            <tr>
                                <td height="45">术前日期</td>
                                <td><input type="text" value="" name="operation_time" id="operation_time" class="datepicker" />  </td>
                            </tr>
                            <tr>
                                <td>公开: </td>
                                <td><input type="radio" name="is" value="1" id="is1" checked/><label for="is1">公开</label><input type="radio" name="is" id="is0" value="0"/><label for="is0">隐藏</label></td>
                            </tr>
                            <tr>
                                <input type="hidden" name="type" value="1"/>
                                <td></td>
                                <td><input type="submit" value="提交" style="padding:2px 5px;margin:10px 0;"></td>
                            </tr>
                        </table>
                    </form>

                </div>
                <script language="javascript">
                    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" }).val();
                    $("#user_id").autocomplete({
                        source: "../topic/Suser",
                        minLength: 2,
                        select: function(event, ui) {
                            $('#uid').val(ui.item.id);
                        }
                    });
                </script>
               
        <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/rl_exp.js"></script>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

    </div>
</div>
