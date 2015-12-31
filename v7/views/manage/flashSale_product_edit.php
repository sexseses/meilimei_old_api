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
                    <li class="on"><a href="<?php echo base_url('manage/flashSale')?>">闪购管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashSale/add')?>">添加闪购活动</a></li>
                </ul>
            </div>

            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                <?php 
                    $hidden = array('act' => 'edit'); 
                    $attributes = array('id' => 'eventaddform');
                ?>
                <?php echo form_open_multipart('manage/flashSale/edit_product',$attributes,$hidden); ?>
                <input type="hidden" name="id" value="<?php echo $id;?>">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">序号</th>
                                <th width="40%">活动名称</th>
                                <th width="10%">排序</th>
                                <th width="20%">操作</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($tehui_rs as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="40%">
                                        <?php echo $value['title']; ?>
                                    </td>
                                    <td width="10%">
                                        <input maxlength="15" name ="in<?php echo $value['id']; ?>" type="text" id="in<?php echo $value['id']; ?>" value="<?php echo $value['level']; ?>">
                                    </td>
                                    <td width="20%">
                                        <a href="<?php echo base_url("manage/flashSale/del_product/$id/{$value['id']}"); ?>">删除</a>
                                         <a href="javascript::" onclick="change(<?php echo $value['id']; ?>,<?php echo $id;?>)"><span id="change" onclick="change(<?php echo $value['id']; ?>,<?php echo $id;?>)">修改</span></a>
                                    </td>
                                </tr>
                                <tr height="10">
                                </tr>
                            <?php }  ?>
                        </tbody>
                    </table>
                </form>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
<script type="text/javascript">
//$(function () {
    function change($data,$fs_id){
        var level = "in" + $data;
        var levelval = $('#'+level).val();

        var url = 'http://www.meilimei.com/manage/flashSale/edit_product/'+$fs_id+"/"+ $data;
        $.ajax({
            url:url,
            type:'post',
            data: "level=" + levelval,
            success:function(data){
                location.reload();
            }
             
        })
    }
//});
</script>
