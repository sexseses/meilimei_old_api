<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/tongji')?>">统计管理</a></li><li><a href="<?php echo site_url('manage/tongji/fenduan')?>">分段统计</a></li>
                                <li><a href="<?php echo site_url('manage/tongji/fenduan2')?>">分段统计2</a></li><li><a href="<?php echo site_url('manage/tongji/online')?>">在线统计2</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form">
                            	<ul> 
                                  <li>
                                	<div class="k1"><span> </span>匿名问题： </div>
                                    <div class="k2"> <?php echo  $question_rows ; ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>用户问题： </div>
                                    <div class="k2"> <?php echo $question_trows-$question_rows ?> </div>
                                 </li><li>
                                	<div class="k1"><span> </span>问题总数： </div>
                                    <div class="k2"> <?php echo $question_trows  ?> </div>
                                 </li>
                                 =================================================================
                                 <li>
                                	<div class="k1"><span> </span>已被回答问题数： </div>
                                    <div class="k2"> <?php echo $eansrows  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>所有回答条数： </div>
                                    <div class="k2"> <?php echo $ansrows  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>继续交谈问题数： </div>
                                    <div class="k2"> <?php echo $talkrows  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>问题回答概率： </div>
                                    <div class="k2"> <?php echo round($eansrows/$question_trows,2)  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>问题继续交流概率： </div>
                                    <div class="k2"> <?php echo round($talkrows/$eansrows,2)  ?> </div>
                                 </li>
                                  =================================================================
                                 <li>
                                	<div class="k1"><span> </span>预约数： </div>
                                    <div class="k2"> <?php   echo $yuyuerows[0]->num  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>非咨询用户预约数： </div>
                                    <div class="k2"> <?php echo $yuyueByUserrows[0]->num ?> </div>
                                 </li>
                                  <li>
                                	<div class="k1"><span> </span>注册用户预约总数： </div>
                                    <div class="k2"> <?php echo $yuyuerows[0]->num-$yuyueByUserrows[0]->num ?> </div>
                                 </li>
                                 
                                 =================================================================
                                 <li>
                                	<div class="k1"><span> </span>普通用户： </div>
                                    <div class="k2"> <?php echo $users[0]->num  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>已联系： </div>
                                    <div class="k2"> <?php echo $contactUser  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>医生用户： </div>
                                    <div class="k2"> <?php echo $users[1]->num  ?> </div>
                                 </li>
                                 <li>
                                	<div class="k1"><span> </span>医院： </div>
                                    <div class="k2"> <?php echo $users[2]->num  ?> </div>
                                 </li>
                                  <li>
                                	<div class="k1"><span> </span>总数： </div>
                                    <div class="k2"> <?php echo $users[0]->num+$users[1]->num+$users[2]->num  ?> </div>
                                 </li>
                                 =================================================================
                                  <li> 
                                	<div class="k1"><span> </span>人均问题： </div>
                                    <div class="k2"> <?php echo round(($question_trows-$question_rows)/$users[0]->num,2)  ?> </div>
                                 </li> 
                                 <li>
                                	<div class="k1"><span> </span>派单比例： </div>
                                    <div class="k2">  
                                    <?php echo round($yuyuerows[0]->num/$users[0]->num,2)  ?>
                                    </div>
                                 </li>
                               
                                </ul>  
                                <div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
