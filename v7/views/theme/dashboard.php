<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css"><div class="page_content932">
            	<div class="institutions_info">
 <?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li class="on"><a href="<?php echo site_url('user/dashboard') ?>" class="to_answer">待回答问题</a></li>
                 <?php   if($roleId==2):?> <li><a href="<?php echo site_url('user/underway') ?>" class="to_answer">参与中的问题</a><?php echo $newans>0?'<div class="newans"><div class="nlf"></div><em>'.$newans.'</em><div class="nrf"></div></div>':'';?></li><?php endif; ?>
                                <li><a href="<?php echo site_url('user/Fquestions') ?>" class="resolved">已解答问题</a></li>
                            </ul>
                        </div>
                        <div class="paging">
                            <div class="paging_right">
                            	<ul>
                                    <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                                    <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                </ul>
                                <h5>第<?php echo $offset ?>-<?php echo $offset+count($questions)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                            </div>
                            <?php if($this->session->userdata('WEN_role_id')==3): ?>
                            <div style="float:left;">
                                <h3>请选择以下代回答的医师账户:</h3>
                                <?php
                                $arr = $this->db->query("select * from users where invite_from = {$this->session->userdata('WEN_user_id')}")->result_array();

                                ?>
                                <style>
                                    #sel_doctor li{
                                        float:left;
                                        padding:5px 5px;
                                    }
                                    #sel_doctor li.current{
                                        background-color:#dd2962;
                                    }
                                </style>
                                <ul id="sel_doctor">
                                    <?php foreach($arr as $k => $v): ?>
                                    <li <?php if($this->session->userdata('yishi_id')==$v['id']) echo "class='current'";  ?> ><a href='<?php echo site_url('user/dashboard/admin_answer/'.$v['id']); ?>' ><?php echo $v['alias'] ?></a></li>
                                    <?php endforeach; ?>
                                    <li><a href="<?php echo site_url('user/logout_doctor') ?>">退出代答状态</a></li>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="question_list">
                        	<ul>
                            <?php 
							    foreach($questions as $row){    
							if($row['id']){
								if($roleId==1){
									 echo '<li> <div class="question_title">
            <h5 class="on">'.( $row['new_answer']>0 ?'<div class="newans"><div class="nlf"></div><em>'.$row['new_answer'].'</em><div class="nrf"></div></div>':'').'<span>[编号:'.$row['id'].']</span> <a href="'.site_url('question/'.$row['id']).'">'.$row['title'].'</a>'.($row['new_answer']>0 && $row['fUid']==$uid?'<em>新</em>':'').'</h5>
                               <p>'.$row['description'].'</p> </div>
                                    <div class="question_state">待解决<br>'.($row['has_answer']==0?'<a class="ansbtnd" href="'.site_url('question/'.$row['id']).'">我来第一个回答</a>':'<a class="ansbtnc" href="'.site_url('question/'.$row['id']).'">我要回答</a>').'</div>
                                    <div class="question_time">
                                    	<ul>
                                        	<li>提问者: ***</li>
                                            <li>提问时间：'.date('Y-m-d',$row['cdate']).'</li>
                                        </ul>
                                    </div>
                                    <div class="clear" style="clear:both;"></div>
                                </li>';	
								}elseif($roleId==2){
									$levtime  = 3 - $row['has_answer'];
									switch($levtime){ 
										case 3:
										 $levtime = '我来第一个回答';
										 break;
										case 2:
										 $levtime = '还有2次机会回答';
										 break;
										case 1:
										 $levtime = '还有1次机会回答';
										 break;
										 default:
										 $levtime = '我要回答';
										 break;
									}
									if(isset($myqid[$row['id']])){
										 $levtime = '我要回答';
									}
									echo '<li> <div class="question_title">
            <h5 class="on">'.((isset($qstates[$row['id']]) && $qstates[$row['id']]>0) ?'<div class="newans"><div class="nlf"></div><em>'.$qstates[$row['id']].'</em><div class="nrf"></div></div>':'').'<span>[编号:'.$row['id'].']</span> <a href="'.site_url('question/'.$row['id']).'">'.$row['title'].'</a>'.($row['new_answer']>0 && $row['fUid']==$uid?'<em>新</em>':'').'</h5>
                               <p>'.$row['description'].'</p> </div>
                                    <div class="question_state">待解决<br><a class="ansbtnd" href="'.site_url('question/'.$row['id']).'">'.$levtime.'</a></div>
                                    <div class="question_time">
                                    	<ul>
                                        	<li>提问者: ***</li>
                                            <li>提问时间：'.date('Y-m-d',$row['cdate']).'</li>
                                        </ul>
                                    </div>
                                    <div class="clear" style="clear:both;"></div>
                                </li>';	
								}else{ 
									 echo '<li> <div class="question_title">
            <h5 class="on"> <span>[编号:'.$row['id'].']</span> <a href="'.site_url('question/'.$row['id']).'">'.$row['title'].'</a>'.($row['new_answer']>0 && $row['fUid']==$uid?'<em>新</em>':'').'</h5>
                               <p>'.$row['description'].'</p> </div>
                                    <div class="question_state">待解决<br>'.($row['has_answer']==0?'<a class="ansbtnd" href="'.site_url('question/'.$row['id']).'">我来第一个回答</a>':'<a class="ansbtnc" href="'.site_url('question/'.$row['id']).'">我要回答</a>').'</div>
                                    <div class="question_time">
                                    	<ul>
                                        	<li>提问者: ***</li>
                                            <li>提问时间：'.date('Y-m-d',$row['cdate']).'</li>
                                        </ul>
                                    </div>
                                    <div class="clear" style="clear:both;"></div>
                                </li>';	
								}
								
							} 
							}
							?> 
                                 
                            </ul>
                        </div>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 