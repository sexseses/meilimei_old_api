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
                    $hidden = array('act' => 'add'); 
                    $attributes = array('id' => 'eventaddform');
                ?>
                <?php echo form_open_multipart('manage/flashSale/add_product',$attributes,$hidden); ?>
                <input type="hidden" name="id" value="<?php echo $id;?>">
                    <table align="center">
                         <thead>
                            <tr>
                                <th width="5%">选择</th>
                                <th width="5%">序号</th>
                                <th width="40%">活动名称</th>
                            </tr>
                        </thead>
                        <tbody align="center">
                            <?php foreach ($tehui_rs as $value) { ?>
                                <tr>
                                    <td width="5%">
                                        <input type="checkbox" name="pro[]" value="<?php echo $value['id']; ?>"
                                        <?php 
                                            if(in_array($value['id'], unserialize($sale_rs['product']))) { 
                                                echo "checked='checked'";
                                            }
                                        ?> />
                                    </td>
                                    <td width="5%">
                                        <?php echo $value['id']; ?>
                                    </td>
                                    <td width="40%">
                                        <?php echo $value['title']; ?>
                                    </td>
                                </tr>
                            <?php }  ?>
                            <tr height="20">
                         
                            </tr>
                                <tr>
                                    <td width="5%"></td>
                                    <td colspan=2 style="float:left"><input type='submit' value="保存"></td>
                                </tr>
                        </tbody>
                    </table>
                </form>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>

