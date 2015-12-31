<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
               <ul>
                    <li><a href="<?php echo site_url('manage/appPush') ?>">推送任务</a></li><li><a href="<?php echo site_url('manage/appPush/add') ?>">添加任务</a></li>
              </ul>
            </div><style type="text/css">.manage_yuyue label{display:inline-block; padding:2px 20px 2px 0;font-size:12px;}</style>
            <div class="clear" style="clear:both;"></div>
            <div class="manage_yuyue">
                <div><?php echo form_open_multipart('manage/appPush/add') ?>
                   
                        <table>
                            <tr>
                                <td width="100">标识：</td>
                                <td> <input name="title" type="text" value=""/></td>
                            </tr>
                            <tr>
                                <td>发送信息</td>
                                <td><textarea name="message" rows="5" cols="50"></textarea></td>
                            </tr>
                            <tr>
                                <td height="45">时间类型</td>
                              <td><select name="datetype">
                              <option value="1">每天</option>
                              <option value="2">指定日期</option>
                              </select></td>
                            </tr>
                            <tr>
                                <td height="45">发送日期</td>
                              <td><input type="text" value="" name="sdate" id="ctime" />  </td>
                            </tr>
                             <tr>
                                <td height="45">发送人员范围</td>
                              <td><select name="usertype">
                              <option value="1">全部</option>
                              <option value="2">指定人员</option>
                              </select></td>
                            </tr>
                            <tr>
                                <td height="45">发送人员</td>
                              <td><textarea name="suser"></textarea>,分隔</td>
                            </tr> 
                            <tr>
                                <td></td>
                                <td><input type="submit" value="提交" style="padding:2px 5px;margin:10px 0;"></td>
                            </tr>
                        </table>
                    </form>

                </div>
                
                 
<script>
  $(function() { 
	$("#user_id").autocomplete({
                source: "../topic/Suser",
                minLength: 2,
                select: function(event, ui) { 
                    $('#suser_id').val(ui.item.id);  
                }
 });
 $( "#ctime" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  });
 
  </script>

            </div>
        </div>
        <div class="clear" style="clear:both;"></div>

    </div>
</div>
