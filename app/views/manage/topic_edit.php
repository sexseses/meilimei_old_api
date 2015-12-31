<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
    KindEditor.ready(function (K) {

        window.editor = K.create('#content',{width:'700px'});
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
            </div><style type="text/css">table li img{max-height:260px;} table li{width:250px;height:auto;margin-bottom:10px;float:left;} .manage_yuyue label{display:inline-block; padding:2px 20px 2px 0;font-size:12px;}</style>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div>
                    <form  method="post" accept-charset="utf-8" enctype="multipart/form-data"><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                        <table>
                            <tr>
                                <td>标题： </td>
                                <td>
                                <textarea name="title" rows="5" cols="50"><?php $t = unserialize($results[0]->type_data); echo $t['title'] ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td>描述</td>
                                <td><textarea name="content" id="content" rows="20" cols="50"><?php echo $results[0]->content ?></textarea></td>
                            </tr>
                            <tr>
                                <td>浏览</td>
                               <td><input type="text" name="views" value="<?php echo $results[0]->views ?>" size="50"></td>
                            </tr>
                            <tr>
                                <td height="45">热度</td>
                              <td><input type="text" value="<?php echo $results[0]->hots ?>" name="hots" id="hots" />  </td>
                            </tr>
                            <tr>
                                <td>标签</td>
                               <td><input type="text" name="tags" value="<?php echo $results[0]->tags ?>" size="50"></td>
                            </tr>
                            <tr>
                                <td>排序</td>
                               <td><input type="text" name="weight" value="<?php echo $results[0]->weight ?>" size="20"></td>
                            </tr>
                            <tr>
                                <td>置顶</td>
                                <td><input type="radio" name="top" id="top" value="1"　<?php if($results[0]->top == 1){ ?> checked="checked" <?php }?>/><label for="top">是</label><input type="radio" id="top1" name="top" value="0"  <?php if($results[0]->top == 0){ ?>checked="checked" <?php }?>/><label for="top1">否</label><input type="text" name="top_days" value="<?php echo $results[0]->top_days ; ?>"/>时间<input name="top_start" type="text" value="<?php  echo date('Y-m-d',$results[0]->top_start); ?>" class="datepicker"><input name="top_end" type="text"  value="<?php  echo date('Y-m-d',$results[0]->top_end); ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td width="50px">小组置顶</td>
                                <td><input type="radio" name="grouptop" id="grouptop" value="1" <?php if($results[0]->grouptop == 1){ ?> checked="checked" <?php }?>/><label for="grouptop">是</label><input type="radio" id="grouptop1" name="grouptop" value="0" <?php if($results[0]->grouptop == 0){ ?> checked="checked" <?php }?>/><label for="grouptop1">否</label><input type="text" name="group_days" value="<?php echo $results[0]->group_days;?>" />时间<input name="group_start" type="text" value="<?php echo date('Y-m-d',$results[0]->group_start);?>" class="datepicker"><input name="group_end" type="text"  value="<?php echo date('Y-m-d',$results[0]->group_end);?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td>推荐</td>
                                <td><input type="radio" name="piazza" id="piazza1" value="1" <?php if($results[0]->piazza == 1){ ?>checked="checked" <?php }?>/><label for="piazza1">是</label><input type="radio" id="piazza2" name="piazza" value="0" <?php if($results[0]->piazza == 0){ ?>checked="checked" <?php }?> /><label for="piazza2">否</label><input type="text" name="piazza_days" value="<?php echo $results[0]->piazza_days;?>" />时间<input name="piazza_start" type="text" value="<?php  echo date('Y-m-d',$results[0]->newtime); ?>" class="datepicker"><input name="piazza_hour" type="text"  value="<?php  $s = split(' ',date('Y-m-d H:i',$results[0]->newtime));echo $s[1]; ?>"></td>
                            </tr>
                            <tr>
                                <td>精华</td>
                                <td><input type="radio" name="chosen" id="chosen1" value="1" <?php if($results[0]->chosen == 1){ ?>checked="checked" <?php }?>/><label for="chosen1">是</label><input type="radio" id="chosen2" name="chosen" value="0" <?php if($results[0]->chosen == 0){ ?>checked="checked" <?php }?> /><label for="chosen2">否</label><input type="text" name="days" value="<?php echo $results[0]->days;?>" />时间<input name="chosen_start" type="text" value="<?php  echo $results[0]->chosen_start; ?>" class="datepicker"><input name="chosen_end" type="text"  value="<?php  echo $results[0]->chosen_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td>最火</td>
                                <td><input type="radio" name="hot" id="hot" value="1" <?php if($results[0]->hot == 1){ ?>checked="checked" <?php }?>/><label for="hot">是</label><input type="radio" id="hot1" name="hot" value="0" checked="checked" <?php if($results[0]->hot == 0){ ?>checked="checked" <?php }?>/><label for="hot1">否</label><input type="text" name="hot_days" value="0" />时间<input name="hot_start" type="text" value="<?php  echo $results[0]->chosen_start; ?>" class="datepicker"><input name="hot_end" type="text"  value="<?php  echo $results[0]->chosen_end; ?>" class="datepicker"></td>
                            </tr>
                            <tr>
                                <td>产品ID</td>
                                <td><input type="text" size="60" value="<?php echo $results[0]->tehui_ids ?>" name="tehui_ids">,分隔</td>
                            </tr>
							<tr>
                                <td colspan="2">闪购IDS <input type="text" size="60" name="fs_ids" value="<?php echo $results[0]->fs_ids ?>">,分隔</td>
                            </tr>
                            <tr>
                                <td>外链IDS</td>
                                <td><input type="text" size="60" value="<?php echo $results[0]->extra_ids ?>" name="extra_ids">,分隔</td>
                            </tr>
                            <tr>
                            <td>图片</td>
                            <td><ul><?php foreach($pictures as $r){
								echo '<li><img src="http://pic.meilimei.com.cn/upload/'.$r->savepath.'" width="200"> <br>
								<input type="text" name="savepath['.$r->id.']" size="30" value="'.$r->savepath.'" />
								 <input type="text" name="picorder['.$r->id.']" size="30" value="'.$r->order.'" />
								 <br>
								 <input type="text" name="picinfo['.$r->id.']" size="30" value="'.$r->info.'" />
								</li>';
							}?></ul>
							<div class="file">
								<input type="file" name="image"  id="image_file"  value="" />
								<div class="step2">
									<div id="showimg"></div>
					            </div>
					        </div>
							<!--<button id="up_img">上传图片</button></td>-->
                            </tr>
                            <tr><td>主图</td>
                            <?php
							 $tmp = unserialize($results[0]->type_data);
							?>
                            <td><textarea style="display:none" name="sourceinfo"><?php echo $results[0]->type_data ?></textarea>
                            <input type="text" style="width:500px;padding:2px;" value="<?php echo isset($tmp['pic']['savepath'])?$tmp['pic']['savepath']:'' ?>" name="mainpc" />
                                <input type="file" name="picture[0]"></td>
                            </tr>
                            <tr>
                                <td>图片: </td>
                                <td><input type="file" name="frontpic" /><img src="http://7xkdi8.com1.z0.glb.clouddn.com/<?php echo $results[0]->front_q_pic; ?>" width="200"><img src="http://pic.meilimei.com.cn/upload/<?php echo $results[0]->front_pic; ?>" width="200"></td>
                            </tr>
                            <tr>
                                <td>文字: </td>
                                <td><input type="text" name="frontdesc" size="100" value="<?php echo $results[0]->front_title; ?>"/></td>
                            </tr>
                            <tr>
                                <td>图片类型: </td>
                                <td><input type="text" name="typepic" value="<?php echo $results[0]->type_pic; ?>"/>1为大图，2为中图，3正常</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><input type="submit" value="修改" style="padding:2px 5px;margin:10px 0;">

                                <input type="submit" name="passSend" value="通过审核保存" style="padding:2px 5px;margin:10px 0;">
                                 <input type="submit" name="deleteinfo" value="删除" style="padding:2px 5px;margin:10px 0;">
                                </td>
                            </tr>
                        </table>
                    </form>

                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

    </div>
</div>
<script type="text/javascript" src="http://bd.meilimei.com/assets/js/jquery.form.js"></script>
<script>
    $(function() {
			var i = 1;
			var showimg = $('#showimg');
			$(".file").wrap("<form id='myupload' action='http://www.meilimei.com/jquery/upImage' method='post' enctype='multipart/form-data'></form>");
			$("#image_file").change(function(){
				$("#myupload").ajaxSubmit({
					dataType:  'json',
					success: function(data) {
						var img = data.banner_pic;
						alert(img);
						showimg.html(data.banner_pic);
					},
					error:function(data){
						showimg.html("上传失败");
						files.html(xhr.responseText);
					}
				});
			});

			$("#addmore").click(function(){
				$("#piclist").append('<tr><td>图片'+i+'</td><td><input type="hidden" name="picture_order['+i+']" value="'+i+'" /><input type="file" name="picture['+i+']"><input type="text" name="vedio['+i+']"><input type="checkbox" name="is_vedio['+i+']" value="1"></td></tr><tr><td>描述'+i+'</td><td><textarea name="picture_info['+i+']" type="text" size="60"></textarea></td></tr>');++i;
			});
			$( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
	})
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
			$(".setcontact").click(function(){
				$.get("../manage/questions/contact/"+$(this).attr('data-id'), {id: $(this).attr('data-id')},
					function (data, textStatus){
					}, "json");$(this).parent().parent().css("background","none");
				$(this).remove();
			});

</script>
