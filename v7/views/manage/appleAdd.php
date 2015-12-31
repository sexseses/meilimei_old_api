
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
                    <?php if(!$id = $this->uri->segment(4)):?>
                    <li class="on"><a href="<?php echo base_url('manage/apple/add')?>">添加广告</a></li>
                    <?php else: ?>
                    <li class="on"><a href="javascript:;">修改广告</a></li>
                    <?php endif; ?>
                    <li ><a href="<?php echo base_url('manage/apple/linksproduct')?>">外链产品</a></li>
                    <li ><a href="<?php echo base_url('manage/apple/addlink')?>">添加外链产品</a></li>
                </ul>
            </div>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div class="comments"><?php echo form_open_multipart('manage/apple/add'); ?>
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
                        <tr><td style="width:30%"><b class="list_title">标题：</b></td><td><input name="title" type="text" style="padding:2px;" value="<?php echo @$row['title'] ?>" size="45"/></td></tr>
                        <tr>
                            <td><b class="list_title">缩略图：320*100</b></td>
                            <td>
                                <input name="picture" type="file" size="45"/>

                                <br>
                                <?php if(@$row['picture']): ?>
                                <input type="hidden" name="oldpicture" value="<?php echo $row['hidden_pic']; ?>">
                                <img style="max-width:600px;" src="<?php echo $row['picture']; ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td><b class="list_title">分享图：</b>200*200</td>
                            <td>
                                <input name="sharepic" type="file" size="45"/>

                                <br>
                                <?php if($row['sharepic']): ?>
                                <input type="hidden" name="oldsharepic" value="<?php echo $row['hidden_sharepic']; ?>">
                                <img style="max-width:300px;" src="<?php echo $row['sharepic']; ?>">
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><td><b class="list_title">广告位：</b></td><td>
                            <ul>
                                <?php  $adPosSel = @explode('$',trim($row['adPos'],'$')); ?>
                            <?php foreach ($adPos as $k=>$v) { ?>
                            <li>
                                <input name="adPos[]" type="checkbox" style="padding:2px;" <?php if(in_array($k,$adPosSel)): ?>checked="checked"<?php endif; ?> value="<?php echo $k?>" size="45"/><?php echo $v; ?>
                            </li>
                            <?php }?>
                        </ul></td></tr>
                        <tr>
                            <td><b style="width: 100px;" class="list_title">添加特惠ID：</b></td><td>
                                <ul>
                                    <li><input name="tehuiid" type="text" style="padding:2px;" value="<?php echo @$row['tehuiid'] ?>" size="10"/></li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><b style="width: 100px;" class="list_title">活动类型：</b></td><td>
                                <ul>
                                    <li><input name="subtype" type="text" style="padding:2px;" value="<?php echo @$row['subtype']; ?>" size="10"/>1墨镜，2,闪购 传闪购ID，3，报名 传event_id，4社区 传event_id</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td><b style="width: 100px;" class="list_title">EVENT_ID：</b></td><td>
                                <ul>
                                    <li><input name="event_id" type="text" style="padding:2px;" value="<?php echo @$row['event_id'] ?>" size="10"/></li>
                                </ul>
                            </td>
                        </tr>

                        <tr>
                            <td><b style="width: 100px;" class="list_title">添加闪购ID：</b></td><td>
                                <ul>
                                    <li><input name="flashid" type="text" style="padding:2px;" value="<?php echo @$row['flashid'] ?>" size="10"/></li>
                                </ul>
                            </td>
                        </tr>
                        <tr><td><b class="list_title">地区：</b></td><td><?php foreach($city as $item) {?><input type="checkbox" name="city[]" id="c<?php echo $item['id'];?>" value="<?php echo $item['city'];?>" <?php if(in_array($item['city'], unserialize($row['area']))) { echo "checked='checked'";}?>/><label for="c<?php echo $item['id'];?>"><?php echo $item['city'];?></label><?php } ?></td></tr>
                        <tr><td><b class="list_title">绑定内容：</b></td><td><input type="text" name="bingid" value="<?php echo $row['spcid']; ?>" /></td></tr>
                        <tr><td><b class="list_title">网址：</b></td><td><input type="text" name="url" value="<?php echo $row['url']; ?>" /></td></tr>
                        <tr><td><b class="list_title">标签：</b></td><td><input type="text" name="tags" value="<?php echo $row['tags']; ?>" /></td></tr>
                        <tr><td><b class="list_title">顺序：</b></td><td><input type="text" name="order" value="<?php echo $row['order']; ?>" /></td></tr>
                        <tr><td><b class="list_title">内容：</b></td><td></td></tr>
                        <tr><td colspan="2"><textarea id="content" style="padding:1px;width:700px;height:550px;" name="content"><?php echo $row['content'];?></textarea></td></tr>
                        <tr><td><b >提交表格内容：</b></td><td></td></tr>
                        <tr><td colspan="2"><textarea id="success_content" style="padding:1px;width:700px;height:550px;" name="success_content"><?php echo @$row['success_content'];?></textarea></td></tr>     
                        <tr><td><b >提交短信内容：</b></td><td></td></tr>
                        <tr><td colspan="2"><textarea id="sms" style="padding:1px;width:700px;height:100px;" name="sms"><?php echo $row['sms'];?></textarea></td></tr>
                        <tr><td><b id="morebtn" class="list_title">添加表格</b><input type="checkbox" <?php echo !empty($survey)?'checked="checked"':'' ?> name="opensur" value="1" />
                        <?php if(!empty($survey)){ ?><input type="hidden" name="hasopensur" value="1" /><?php }?>
                        </td><td></td></tr>
                        <tr><td id="moreinput"><?php $i=0;  foreach($survey as $r) {echo '<div><input type="text" value="'.$r['title'].'" name="surver['.$i.']"><span class="del-text">del</span></div>';$i++;} ?></td><td></td></tr>
						<tr><td>邮箱：<input type="text" style="width:350px" name="emails" value="<?php echo $row['email']; ?>" />分隔,</td></tr>
                        <tr><td>标题：<input type="text" style="width:350px" name="sur_title" value="<?php echo $row['sur_title']; ?>" /></td></tr>
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

