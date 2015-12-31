
<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">数据导入管理</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                            <ul style="line-height:180%;text-indent:2em;padding:10px;font-size:20px;">
                                <li>最土用户数据同步：<span style="font-size:12px;">(将美丽美MLzhensuo数据库的users表中的普通用户数据同步到最土特惠zuitu_db数据库b的user表中)</span><input type="button" value="开始同步" onclick="location.href='<?php echo site_url('manage/syncUserInfo') ?>'"></li>
                                <li>导入医院坐标：<input type="button" value="开始导入" onclick="location.href='<?php echo site_url('manage/importCoordinate'); ?>'"></li>

                                <li style="border:1px solid #F00;">悦美网医师数据采集:

                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤1。请先获取城市列表：<input type="button" value="点击获取" onclick="$('#caiji_city').load('<?php echo site_url('manage/yuemei/getDoctorCityList'); ?>');"></span></p>

                                    <form action="<?php echo site_url('manage/yuemei/index') ?>" target="_blank" method="post" >
                                    <p style="text-indent:4em;">
                                        <span style="font-size:12px;">步骤2.采集列表页。先选择城市：<span id="caiji_city"></span><input type="submit" value="开始采集"></span>
                                    </p>
                                    </form>
                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤3.采集详情页。<input type="submit" value="开始采集" onclick="window.open('<?php echo site_url('manage/yuemei/detail'); ?>');"></span></p>
                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤4.导入数据表。<input type="submit" value="开始导入" onclick="$('#caiji_city').empty();window.open('<?php echo site_url('manage/yuemei/importYisheng'); ?>');"></span></p>
                                </li>
                                <li style="border:1px solid #F00;margin-top:10px;">悦美网医院数据采集:

                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤1。请先获取城市列表：<input type="button" value="点击获取" onclick="$('#caiji_hospital_city').load('<?php echo site_url('manage/yuemei/getHospitalCityList'); ?>');"></span></p>

                                    <form action="<?php echo site_url('manage/yuemei/hospitalList') ?>" target="_blank" method="post" >
                                        <p style="text-indent:4em;">
                                            <span style="font-size:12px;">步骤2.采集列表页。先选择城市：<span id="caiji_hospital_city"></span><input type="submit" value="开始采集"></span>
                                        </p>
                                    </form>
                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤3.采集详情页。<input type="submit" value="开始采集" onclick="window.open('<?php echo site_url('manage/yuemei/hospitalDetail'); ?>');"></span></p>
                                    <p style="text-indent:4em;"><span style="font-size:12px;">步骤4.导入数据表。<input type="submit" value="开始导入" onclick="$('#caiji_hospital_city').empty();window.open('<?php echo site_url('manage/yuemei/importJigou'); ?>');"></span></p>
                                </li>
                            </ul>

                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
