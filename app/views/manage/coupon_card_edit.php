<?php  
if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg; 
} 
?> 
<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li><a href="<?php echo base_url('manage/coupon_card')?>">优惠券管理</a></li>
                    <li class="on"><a href="<?php echo base_url('manage/coupon_card/coupon_card_add')?>">增加优惠券</a></li>
                </ul>
            </div>
            <?php 
                $hidden = array('act' => 'edit'); 
                $attributes = array('id' => 'eventaddform');
            ?>
            <?php echo form_open_multipart('manage/coupon_card/coupon_card_edit',$attributes,$hidden); ?>
            <input type="hidden" name="batch" value="<?php echo $batch;?>">
            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                <table>
                    <tr>
                        <td width="150">代金券面额：</td>
                        <td><input type="input" name="money"  id="money" size="28"  value="<?php echo $coupon_rs['credit']; ?>" /><span style="color:red">*</span></td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="150">使用限额：</td>
                        <td>
                            <input type="input" name="quota"  id="quota" size="28"  value="<?php echo $coupon_rs['quota']; ?>"/>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                        <td width="150">生成数量：</td>
                        <td>
                            <input type="input" name="quantity"  id="quantity" size="28"  value="<?php echo $coupon_num; ?>" disabled="" />
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                         <td width="150">开始时间-结束时间 </td>
                         <td>
                            <input type="input" name="begin"  id="begin" size="28" value="<?php echo date('Y-m-d',$coupon_rs['begin_time']); ?>" /> - <input type="input" name="end"  id="end" size="28" value="<?php echo date('Y-m-d',$coupon_rs['end_time']); ?>"/>
                        </td>
                    </tr>
                    <tr height="20">
                         
                    </tr>
                    <tr>
                    <td width="150">批次：</td>
                        <td>
                            <input type="input" name="batch"  id="batch" size="28" disabled="" value="<?php echo $coupon_rs['batch']; ?>"/> 
                        </td>
                    </tr>
                      
                    <tr>
                        <td></td>
                        <td><input type="submit" value="修改" style="padding:2px 5px;margin:10px 0;"></td>
                    </tr>
                </table>
                </div>
            </div>
            </form>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
<script>
$(function() { 
$( "#begin" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
$( "#end" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
});
</script>