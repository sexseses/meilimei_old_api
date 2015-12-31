<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function (K) {
        window.editor = K.create('#content');
		window.editor = K.create('#success_content');
    });
</script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li><a href="<?php echo base_url('manage/apple')?>">广告管理</a></li> 
                    <li ><a href="<?php echo base_url('manage/apple/add')?>">添加广告</a></li>  
                    <li><a href="<?php echo base_url('manage/apple/linksproduct')?>">外链产品</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/apple/addlink')?>">添加外链产品</a></li>
                </ul>
            </div>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div class="comments"><?php echo form_open_multipart('manage/apple/addlink'); ?>
                    <?php if($id = $this->uri->segment(4)):?>
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                    <?php endif; ?>
                    <style>
                        .list_title{

                            display:inline-block;
                            width:75px;
                            text-align:justify;
                            text-justify:distribute-all-lines;/*ie6-8*/
                            text-align-last:justify;/* ie9*/
                            -moz-text-align-last:justify;/*ff*/
                            -webkit-text-align-last:justify;/*chrome 20+*/
                            padding-left:10px;
                        }
                    </style>
                    <table style="line-height:240%;">
                        <tr><td style="width:30%"><b class="list_title">标题：</b></td><td><input name="taobao_title" type="text" style="padding:2px;" value="" size="45"/></td></tr>
                        <tr>
                            <td><b class="list_title">缩略图：220*120</b></td>
                            <td>
                                <input name="taobao_pic" type="file" size="45"/>  
                            </td>
                        </tr>
                        <tr><td><b class="list_title">链接：</b></td><td><textarea cols="50" name="taobao_web"></textarea></td></tr>
                        <tr><td><b class="list_title">价格：</b></td><td><input type="text" name="taobao_price" value="" /></td></tr> 
                        <tr><td><b class="list_title">原来价格：</b></td><td><input type="text" name="taobao_mkprice" value="" /></td></tr> 
                        <tr><td colspan="2"><input type="submit" name="submit" value="添加" style="padding:2px 10px;"/></td></tr>
                    </table>

                    </form>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
        <script type="text/javascript"> 
$(function(){ 
 var i = <?php echo $i ?>; 
 $('#morebtn').click(function(){ 
  if(i < 6) { 
   $('#moreinput').append('<div><input type="text" name="surver[' + i + ']"/><span class="del-text">del</span></div>'); 
   i++; 
  } else { 
   alert("最多加6个"); 
  } 
  
 }); 
 $('.del-text').live('click',function(){ 
 $(this).parent().remove(); 
 i--; 
 }); 
}); 
</script> 
    </div>
</div> 