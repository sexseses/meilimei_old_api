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
                //$hidden = array('act' => 'add'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart("manage/onetehui/onetehui_edit_note",$attributes); ?>
            <div class="manage_yuyue">
            <input type="hidden" name="event_id" value="<?php echo $id;?>">
                <table>
                    <tr>
                        <td>项目：   </td>
                        <td>
                            <?php foreach($item_data as $item) {?>
                                <input datatype="*" errormsg="请选择项目" nullmsg="请选择项目"  type="radio" name="item_name" id=" " value="<?php echo $item['name'];?>"
                                <?php 
                                    if($item['name']==$row['relation_note_item']) { 
                                        echo "checked='checked'";}
                                ?>/>
                                <label><?php echo $item['name'];?></label>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="下一步" style="padding:2px 5px;margin:10px 0;"></td>
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