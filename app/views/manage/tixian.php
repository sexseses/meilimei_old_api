<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css">
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_shortcuts">
                <ul>
                    <li style="float:left;"><a href="#">客户提现记录</a></li>
                    <li style="float:right;"></li>
                </ul>
            </div>
            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                    <div class="manage_search">
                        <form action="<?php echo base_url() ?>manage/tixian" method="get">
                            <ul>
                                <li>手机<input name="phone" type="text"></li>
                                <li>银行卡号<input name="card_num" type="text"></li>
<!--                                <li>时间<input name="yuyueDateStart" type="text" class="datepicker"></li>-->
<!--                                <li><input name="yuyueDateEnd" type="text" class="datepicker"></li>-->
                                <li><input name="" type="submit" value="搜索" class="search"></li>
                            </ul>
                        </form>
                    </div>
                <table style="width:100%;">
                    <tr><th>手机号</th><th>提现类型</th><th>银行卡号</th><th>开户行</th><th>开户姓名</th><th>金额</th><th>管理</th></tr>
                    <?php
                    foreach ($data as $row) {
                        ?>
                             <tr>
                                 <td><?php echo $row['phone']; ?></td>
                                 <td><?php echo $row['ac_type']==0?'支付宝':'银行卡'; ?></td>
                                 <td><?php echo $row['card_num']; ?></td>
                                 <td><?php echo $row['bank']; ?></td>
                                 <td><?php echo $row['ac_name']; ?></td>
                                 <td><?php  echo $row['amount']; ?></td>
                                 <td></td>
                             </tr>
                    <?php } ?>
                </table>
                    <div class="clear" style="clear:both;"></div>
                </div>
                <div class="paging">
                    <div class="paging_right">
                        <ul>
                            <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                            <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                        </ul>
                        <h5>第<?php echo $offset ?>-<?php echo $offset + count($data) - 1 ?>个，共<?php echo $total_rows ?>
                            个</h5>
                    </div>
                </div>
            </div>
        </div>



        <div class="clear" style="clear:both;"></div>
    </div>
</div>
<script>

    $("tr").mouseover(function(){
        $(this).css({'background-color':'red'});
    });
    $("tr").mouseout(function(){
        $(this).css({'background-color':''});
    });
</script>