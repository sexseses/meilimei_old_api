<?php  if ($msg = $this->session->flashdata('flash_message')) {
    echo $msg;
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css"><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/thickbox-compressed.js"></script>
<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script>
<div class="page_content937">
    <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_shortcuts">
                <ul>
                    <li style="float:left;"><a href="#">客户消费记录</a></li>
                    <li style="float:right;"></li>
                </ul>
            </div>
            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                    <div class="manage_search">
                        <form action="<?php echo base_url() ?>manage/fanli" method="get">
                            <ul>
                                <li>手机<input name="mobile" type="text"></li>
<!--                                <li>时间<input name="yuyueDateStart" type="text" class="datepicker"></li>-->
<!--                                <li><input name="yuyueDateEnd" type="text" class="datepicker"></li>-->
                                <li><input name="" type="submit" value="搜索" class="search"></li>
                            </ul>
                        </form>
                    </div>
                <script>
                    function audit(id,type,rate){      //审核消费
                        var msg = type==1?"通过审核":"退回";
                        if(true==confirm("确定"+msg+"吗？")){
                            $.post('<?php echo site_url('manage/fanli/ajax_audit'); ?>',{id:id,type:type},function(json){
                                if(json.status==1){
                                    alert(json.msg);
                                    location.reload();
                                }else{
                                    alert(json.msg);
                                }

                            },'json');
                        }
                    }
                </script>
                <table style="width:100%;">
                    <tr><th>姓名</th><th>手机号</th><th>消费金额</th><th>消费时间</th><th>状态</th><th>管理</th></tr>
                    <?php
                    foreach ($data as $row) { 
                        ?>
                             <tr>
                                 <td><?php echo $row['username']; ?></td>
                                 <td><?php echo $row['mobile']; ?></td>
                                 <td><?php echo $row['amount']; ?></td>
                                 <td><?php echo $row['consum_time']; ?></td>
                                 <td><?php echo $row['fan_status']=='0'?'待审核':($row['fan_status']=='1'?'审核已通过':'已退回'); ?></td>
                                 <td><a target="_blank" href="<?php echo base_url(); ?>upload/fanli/<?php echo $row['image']; ?>">查看凭证</a><br>
             <?php if($row['deal_state']==0){ ?>  <a href="<?php echo site_url('manage/fanli/Gfanli/'.$row['id']) ?>?height=220&width=400" class="thickbox" >通过</a> <a href="javascript:;" onclick="audit(<?php echo $row['id']; ?>,0);">退回</a><?php } ?></td>
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