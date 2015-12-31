<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} ?>
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
                $hidden = array('act' => 'add'); 
                $attributes = array('id' => 'tehuiaddform');
            ?>
            <?php echo form_open_multipart("manage/onetehui/onetehui_edit_note/",$attributes,$hidden); ?>
            <div class="manage_yuyue">
            <input type="hidden" name="event_id" value="<?php echo $id;?>">
            <input type="hidden" name="item_name" value="<?php echo $item_name;?>">
                <table>
                    <tr>
                        <td>项目： </td>
                        <td>
                            <?php if(!empty($note_data)){ ?>
                                <?php foreach($note_data as $note) {?>
                                <input datatype="*" errormsg="请选择日志" nullmsg="请选择日志"  type="checkbox" name="note[]" id="" value="<?php echo $note['nid'];?>"
                                <?php 
                                    if(in_array($note['nid'], unserialize($row['relation_note']))) { 
                                        echo "checked='checked'";}
                                ?>/>
                                <label><img src="http://pic.meilimei.com.cn/upload/<?php echo $note['imgurl'] ?>"  alt="<?php echo $note['name'];?>" /></label>
                                <label><?php echo $note['content'];?></label>
                                <br/>
                                <?php } ?>
                            <?php 
                            }else{
                                echo "该项目没有日志！";
                            } ?>
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