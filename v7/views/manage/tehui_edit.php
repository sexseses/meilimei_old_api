<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >
<script charset="utf-8" src="http://static.meilimei.com.cn/public/js/Validform_532_min.js"></script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/tehui')?>">特惠活动管理</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/tehui/tehui_add')?>">添加特惠活动</a></li>
                </ul>
            </div>
            <?php 
                $hidden = array('act' => 'edit'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart("manage/tehui/tehui_edit",$attributes,$hidden); ?>
            <div class="manage_yuyue">
            <input type="hidden" name="event_id" value="<?php echo $event_id;?>">
                <table>
                    <!-- <tr>
                        <td>地区： </td>
                        <td>
                            <?php foreach($city as $item) {?>
                            <input datatype="*" errormsg="请选择地区" nullmsg="请选择地区"  type="checkbox" name="city[]" id="c<?php echo $item['id'];?>" value="<?php echo $item['city'];?>"
                            <?php 
                                //if(in_array($item['city'], unserialize($row['city']))) { 
                                //    echo "checked='checked'";}
                            ?>/>
                            <label for="c<?php echo $item['id'];?>">
                            <?php echo $item['city'];?></label>
                            <?php } ?>
                        </td>
                    </tr> -->
                    <tr>
                        <td width="200">关联团购项目(填写id)</td>
                        <td>
                        <input type="input" value="<?php echo $row['tehui_id']?>" name="tehuiid"  id="tehuiid" size="32" datatype="s5-16" /><span style="color:red">*</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">团购价（预约价）：</td>
                        <td><input type="input" name="reser_price" class="file" id="reser_price" size="28" value="<?php echo $row['reser_price']?>"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td width="100">定金（对应团购后台的团购价格）：</td>
                        <td><input type="input" name="deposit" class="file"  size="28" value="<?php echo $row['deposit']?>"/><span style="color:red">*</span></td>
                    </tr>
                    <tr>
                        <td style="height:5px"></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>特惠细项目</td>
                        <td>
                            <?php foreach($cate as $item) {?>
                            <input datatype="*"  type="checkbox" name="cate[]"  value="@<?php echo $item['id'];?>@" 
                            <?php
                                    $ids = "@";
                                    $ids .= $item['id'];
                                    $ids .= "@";
                                    if(in_array($ids, explode(",", $row['sub_ids']))) { 
                                        echo "checked='checked'";
                                    }
                            ?>/>
                            <label for="c<?php echo $item['id'];?>">
                            <?php echo $item['name'];?></label>
                            <?php } ?>
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