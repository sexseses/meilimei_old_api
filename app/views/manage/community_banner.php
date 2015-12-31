<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/rl_exp.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/new_css/validform.css" >

<div class="page_content937">
    <div class="institutions_info new_institutions_info">
        <?php  $this->load->view('manage/leftbar'); ?>
        <div class="manage_center_right">
            <div class="question_nav">
                <ul>
                    <li ><a href="<?php echo base_url('manage/community')?>">社区活动管理</a></li>
                    <li ><a href="<?php echo base_url('manage/community/add')?>">添加社区活动</a></li>
                    <li class="on"><a href="#">上传图片</a></li>
                </ul>
        	</div>
            <?php 
                $hidden = array('act' => 'edit'); 
                $attributes = array('id' => 'communityaddform');
            ?>
            <?php echo form_open_multipart('manage/community/edit_banner',$attributes,$hidden); ?>
            <input type="hidden" name="id" value="<?php echo $id;?>">
        	<div class="manage_yuyue">
                <table>
                    <tr>
                        <td><img style="max-width:300px;" src="<?php echo $this->remote->show($community_rs['event_pic']) ?>"><a href = "../del_banner/<?php echo $id;?>?type=1">删除图片</a></td>
                    </tr>
                    
                    <tr height="20">
                    </tr>
                    <tr>
                        <td>
                            <input name="pic" type="file" size="45"/>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="submit" value="保存" style="padding:2px 5px;margin:10px 0;"></td>
                    </tr>
                </table>
        	</div>
            </form>
        </div>
        <div class="clear" style="clear:both;"></div>
    </div>
</div>
