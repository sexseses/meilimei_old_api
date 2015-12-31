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
                $attributes = array('id' => 'productaddform');
            ?>
            <?php echo form_open_multipart("manage/tehui/tehui_edit_product",$attributes,$hidden); ?>
            <div class="manage_yuyue">
            <input type="hidden" name="event_id" value="<?php echo $event_id;?>">
                <table>
                    <tr>
                        <td>商品： </td>
                        <td>
                            <?php foreach($product_data as $product) {?>
                            <input type="checkbox" name="product[]" id="product"  value="<?php echo $product['id'];?>"
                            <?php 
                                if(in_array($product['id'], unserialize($row['relation_product']))) { 
                                    echo "checked='checked'";}
                            ?>/>
                            <label><?php echo $product['title'];?></label>
                            <br/>
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
 
    //$("#eventaddform").Validform();
});
</script>