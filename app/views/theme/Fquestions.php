<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css"><div class="page_content932">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('user/dashboard') ?>" class="to_answer">待回答问题</a></li>  <?php $roleid = $this->wen_auth->get_role_id(); if($roleid==2):?> <li><a href="<?php echo site_url('user/underway') ?>" class="to_answer">参与中的问题</a><?php echo $newans>0?'<div class="newans"><div class="nlf"></div><em>'.$newans.'</em><div class="nrf"></div></div>':'';?></li><?php endif; ?> 
                                <li class="on"><a href="<?php echo site_url('user/Fquestions') ?>" class="resolved">已解答问题</a></li>
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
                        </div>
                        <div class="question_list">
                        	<ul>
                            <?php foreach($questions as $row){
							  echo '<li>
                                    <div class="question_title">
                                        <h5 class="on"><a href="'.site_url('question/view/'.$row['id']).'">'.$row['title'].'</a></h5>
                                        <p>'.$row['description'].'</p>
                                    </div>
                                    <div class="question_state1">'.($row['state']==8?'已关闭':$row['state']==4?'已过期':'已解决').'</div>
                                    <div class="question_time">
                                    	<ul>
                                        	<li>提问者：***</li>
                                            <li>提问时间：'.date('Y-m-d',$row['cdate']).'</li>
                                        </ul>
                                    </div>
                                    <div class="clear" style="clear:both;"></div>
                                </li>';	
							}
							?> 
                                 
                            </ul>
                        </div>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 