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
                    <li><a href="<?php echo site_url('manage/topic');?>">话题管理</a></li>
                    <li class="on"><a href="#">添加话题</a></li>
                </ul>
            </div><style type="text/css">.manage_yuyue label{display:inline-block; padding:2px 20px 2px 0;font-size:12px;}</style>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div><?php echo form_open_multipart('manage/topic/add') ?>
                   
                        <table>
                            <tr>
                                <td width="100">正文(标题)：</td>
                                <td> <textarea style="padding:2px;" id="rl_exp_input" name="title" rows="3" cols="50"></textarea>
                                <br />
                                <a href="javascript:void(0);" id="rl_exp_btn">表情</a>
                                
                                <div class="rl_exp" id="rl_bq" style="display:none;">
		<ul class="rl_exp_tab clearfix">
			<li><a href="javascript:void(0);" class="selected">默认</a></li>
			<li><a href="javascript:void(0);">拜年</a></li> 
		</ul>
		<ul class="rl_exp_main clearfix rl_selected"></ul>
		<ul class="rl_exp_main clearfix" style="display:none;"></ul> 
		<a href="javascript:void(0);" class="close">×</a>
	</div>
                                </td>
                            </tr>
                            <tr>
                                <td>描述</td>
                                <td><textarea name="description" id="description" rows="20" cols="50"></textarea></td>
                            </tr>
                            <tr>
                                <td height="45">发布日期</td>
                              <td><input type="text" value="" name="ctime" id="ctime" class="datepicker" />  </td>
                            </tr>
                            <tr>
                                <td height="45">浏览次数</td>
                              <td><input type="text" value="<?php echo rand(10,50) ?>" name="views" id="views" />  </td>
                            </tr>
                            <tr>
                                <td height="45">热度</td>
                              <td><input type="text" value="0" name="hots" id="hots" />  </td>
                            </tr>
                            <tr>
                            </tr>
                            <tr>
                                <td height="45">发布时间</td>
                              <td>  <select name="extrat">
                              <?php 
							    for($i=1;$i<24;$i++){ echo '<option value="'.$i.'">'.$i.'</option>';}
							  ?></select>:
                              <select name="extrat1"> <?php 
							    for($i=1;$i<61;$i++){ echo '<option value="'.$i.'">'.$i.'</option>';}
							  ?></select></td>
                            </tr>
                            <tr>
                                <td height="45">发布的用户</td>
                              <td><input type="text" value="" name="user_id" id="user_id" /><input type="hidden" id="suser_id" name="suser_id" value="" /></td>
                            </tr>
                            </table>
                           =======================================================
                            <table id="piclist">
                            <tr>
                                <td>图片</td>
                                <td><input type="hidden" name="picture_order[0]" value="1" /><input type="file" name="picture[0]"><input type="text" name="vedio[0]"><input type="checkbox" name="is_vedio[0]" value="1"></td>
                            </tr>
                            <tr>
                                <td>描述0</td>
                                <td><textarea name="picture_info[0]" type="text" size="60"></textarea></td>
                            </tr> 
                            </table>
                            <table>
                            <tr>
                              <td></td><td><a href="javascript:;" id="addmore">增加</a></td>
                              </tr> 
                            <tr>
                                <td>分类</td>
                                <td>  
                                    <?php $type = $this->db->query("select * from items")->result_array(); 
function getson(&$type,$pid){ 
		 foreach($type as $k){
			 if($k['pid']==$pid){
				 $tmph.='<label style="padding-right:10px"><input type="checkbox" value="'.$k['name'].'" name="positions[]">'.$k['name'].'</label>';
				 $tmph.=getson($type,$k['id']);
			 } 
	     } 
		 return $tmph;
  }
  foreach($type as $r){
	  if($r['pid']==0){
		  echo '<li><label style="padding-right:10px"><input type="checkbox" value="'.$r['name'].'" name="positions[]">'.$r['name'].'</label>';
		  echo getson($type,$r['id']).'</li>';
	  }
    } 
  ?>
                             </td>
                            </tr>
                            <tr>
                                <td width="50px">置顶</td>
                                <td><input type="radio" name="top" id="top" value="1"/><label for="top">是</label><input type="radio" id="top1" name="top" value="0"/><label for="top1">否</label><input type="text" name="top_days" value="0" />时间<input name="top_start" type="text" value="<?php  echo $top_start; ?>" class="datepicker"><input name="top_end" type="text"  value="<?php  echo $top_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td width="50px">小组置顶</td>
                                <td><input type="radio" name="grouptop" id="grouptop" value="1"/><label for="grouptop">是</label><input type="radio" id="grouptop1" name="grouptop" value="0"/><label for="grouptop1">否</label><input type="text" name="group_days" value="0" />时间<input name="group_start" type="text" value="<?php  echo $group_start; ?>" class="datepicker"><input name="group_end" type="text"  value="<?php  echo $group_end; ?>" class="datepicker"></td>
                            </tr>

                            <tr>
                                <td>推荐</td>
                                <td><input type="radio" name="piazza" id="piazza1" value="1"/><label for="piazza1">是</label><input type="radio" id="piazza2" name="piazza" value="0"/><label for="piazza2">否</label><input type="text" name="piazza_days" value="0" />时间<input name="piazza_start" type="text" value="<?php  echo $piazza_start; ?>" class="datepicker"><input name="piazza_end" type="text"  value="<?php  echo $piazza_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td>精华</td>
                                <td><input type="radio" name="chosen" id="chosen1" value="1"/><label for="chosen1">是</label><input type="radio" id="chosen2" name="chosen" value="0" checked="checked"/><label for="chosen2">否</label><input type="text" name="days" value="0" />时间<input name="chosen_start" type="text" value="<?php  echo $chosen_start; ?>" class="datepicker"><input name="chosen_end" type="text"  value="<?php  echo $chosen_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td>最火</td>
                                <td><input type="radio" name="hot" id="hot" value="1"/><label for="hot">是</label><input type="radio" id="hot1" name="hot" value="0" checked="checked"/><label for="hot1">否</label><input type="text" name="hot_days" value="0" />时间<input name="hot_start" type="text" value="<?php  echo $chosen_start; ?>" class="datepicker"><input name="hot_end" type="text"  value="<?php  echo $chosen_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td colspan="2">产品IDS <input type="text" size="60" name="tehui_ids" value="">,分隔</td>
                            </tr>
                             <tr>
                                <td colspan="2"><a id="openextrlink" href="javascript:return false;">点击打开添加外链产品</a></td>
                               
                            </tr>
                            <tr><td colspan="2"><ul id="extrapruduct" style="display:none">
                                <li>标题: <input style="width:600px" type="text" name="extrapruduct_title" /></li>
                                <li>价格: <input type="text" name="extrapruduct_price" /></li>
                                <li>市场价: <input type="text" name="extrapruduct_mprice" /></li>
                                <li>外链: <textarea style="width:600px"  name="extrapruduct_link"></textarea></li>
                                <li>图片: <input type="file" name="extrapruduct_picture" /></li> 
                                </ul></td></tr>
                             <tr><td colspan="2">外链IDS  <input type="text" size="60" name="extra_ids" value="">,分隔</td>
                            </tr>  
                        </table>
                        <table>
                         <tr>
                                <td colspan="2">视频信息 
                                <textarea name="video" id="content" style="width:800px" ></textarea> </td>
                            </tr>
                         <tr>
                                <td colspan="2">视频高度 
                             <input name="videoHeight" type="text" value="0" /> </td>
                            </tr>
                              
                            <tr>
                                <td>图片: </td>
                                <td><input type="file" name="frontpic" /></td>
                            </tr>
                            <tr>
                                <td>文字: </td>
                                <td><input type="text" name="frontdesc" size="100"/></td>
                            </tr>
                            <tr>
                                <td>图片类型: </td>
                                <td><input type="text" name="typepic"/>1为大图，2为中图，3正常</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" value="提交" style="padding:2px 5px;margin:10px 0;"></td>
                            </tr>
                        </table>
                    </form>

                </div>
                
               
<script>
    $(".datepicker").datepicker({ dateFormat: "yy-mm-dd" }).val();
    $("#selectall").click(function(){
        $(".manage_yuyue_form :checkbox").attr("checked",true);
    });
    $("#selectnone").click(function(){
        $(".manage_yuyue_form :checkbox").attr("checked",false);
    });
    $("#deleteid").click(function(){
        $(".manage_yuyue_form :checkbox:checked").each(function(){
            $(this).parent().parent().hide(300);
            $.get("../manage/questions/del/"+$(this).val(), {id: $(this).val()},
                function (data, textStatus){
                }, "json");
        })
    })

    $(".setcontact").click(function() {
        $.get("../manage/questions/contact/" + $(this).attr('data-id'), {id: $(this).attr('data-id')},
            function (data, textStatus) {
            }, "json");
        $(this).parent().parent().css("background", "none");
        $(this).remove();
    });
$(function() {
  var i = 1;
    $("#addmore").click(function(){
		$("#piclist").append('<tr><td>图片'+i+'</td><td><input type="hidden" name="picture_order['+i+']" value="'+i+'" /><input type="file" name="picture['+i+']"><input type="text" name="vedio['+i+']"><input type="checkbox" name="is_vedio['+i+']" value="1"></td></tr><tr><td>描述'+i+'</td><td><textarea name="picture_info['+i+']" type="text" size="60"></textarea></td></tr>');++i;
	});
	$("#openextrlink").click(function(){
		$("#extrapruduct").toggle();
	})
	$("#user_id").autocomplete({
                source: "../topic/Suser",
                minLength: 2,
                select: function(event, ui) {
                    $('#suser_id').val(ui.item.id);
                }
    });
     //$( "#ctime" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
 
  </script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/rl_exp.js"></script>  
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

    </div>
</div>
