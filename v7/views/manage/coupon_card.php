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
                    <li class="on"><a href="<?php echo base_url('manage/coupon_card')?>">优惠券管理</a></li>
                    <li><a href="<?php echo base_url('manage/coupon_card/coupon_card_add')?>">添加优惠券</a></li>
                </ul>
            </div>
            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                    <table align="center">
                         <thead>
                            <tr>
                                 
                                <th width="20%">批次</th>
                                <th width="10%">开始时间</th>
                                <th width="10%">结束时间</th>
                                <th width="10%">消费金额</th>
                                <th width="10%">限制金额</th>
                                <th width="20%">操作</th>
                            </tr>
                            <tr height="10px">
                                </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($coupon_rs as $value) { ?>
                                <tr>
                                     
                                    <td width="20%">
                                        <?php echo $value['batch']?>
                                    </td>
                                    <td width="10%">
                                        <?php echo date('Y-m-d', $value['begin_time']); ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo date('Y-m-d', $value['end_time']); ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo $value['credit']; ?>
                                    </td>
                                    <td width="10%">
                                        <?php echo $value['quota']; ?>
                                    </td>
                                    <td width="40%">
                                        <!-- <a onclick="return confirm(\'确认删除 ' . $value['batch'] . '?\')" href="<?php echo base_url("manage/coupon_card/del/{$value['batch']}"); ?>">删除批次</a>  -->
                                        <a href="<?php echo base_url("manage/coupon_card/coupon_card_edit/{$value['batch']}"); ?>">编辑</a>
                                        <a href="<?php echo base_url("manage/coupon_card/coupon_card_list/{$value['batch']}"); ?>">详细列表</a>
                                    </td>
                                </tr>
                                <tr height="10px">
                                </tr>
                            <?php }  ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>

