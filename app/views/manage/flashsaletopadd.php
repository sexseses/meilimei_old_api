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
                    <li class="on"><a href="<?php echo base_url('manage/flashsaletop')?>">闪购管理</a></li>
                    <li><a href="<?php echo base_url('manage/flashsale/add')?>">添加闪购活动</a></li>
 
                </ul>
            </div>

            <div class="manage_yuyue">
                <div class="manage_yuyue_form">
                    <table align="center">
                        <thead>
                        <tr>
                            <th width="5%">左</th>
                            <th width="20%">右</th>  
                        </tr>
                        </thead>
						<?php echo form_open_multipart("manage/flashsaletop/add",$attributes,$hidden); ?>
                        <tbody align="center">
							<tr>
                                <td width="5%">
                                    新的id
                                </td>
                                <td width="20%">
                                    <input type="input" name="new_fs_id"  id="" size="28" />
                                </td>
                            </tr>
							<tr>
								<td><input type="submit" value="保存" style="padding:2px 5px;margin:10px 0;"></td>
                            </tr>
						</form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
