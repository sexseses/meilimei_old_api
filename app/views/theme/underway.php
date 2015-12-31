<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css"><div class="page_content932">
            	<div class="institutions_info">
 <?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo site_url('user/dashboard') ?>" class="to_answer">待回答问题</a></li>
                 <?php  if($roleId==2):?>    <li class="on"><a href="<?php echo site_url('user/underway') ?>" class="to_answer">参与中的问题</a><?php echo $newans>0?'<div class="newans"><div class="nlf"></div><em>'.$newans.'</em><div class="nrf"></div></div>':'';?></li><?php endif; ?>
                                <li><a href="<?php echo site_url('user/Fquestions') ?>" class="resolved">已解答问题</a></li>
                            </ul>
                        </div><div class="question_list">
                         <ul>
                            <?php foreach($uquestions as $r){  
							 if($r['id']){
								 echo '<li> <div class="question_title">
            <h5 class="on">'.((isset($qstates[$r['id']]) && $qstates[$r['id']]>0)?'<div class="newans"><div class="nlf"></div><em>'.$qstates[$r['id']].'</em><div class="nrf"></div></div>':'').'<span>[编号:'.$r['id'].']</span> <a href="'.site_url('question/'.$r['id']).'">'.$r['title'].'</a>'.($r['new_answer']>0 && $r['fUid']==$uid?'<em>新</em>':'').'</h5>
                               <p>'.$r['description'].'</p> </div>
                                     
                                    <div class="question_time">
                                    	<ul>
                                        	<li>提问者: ***</li>
                                            <li>提问时间：'.date('Y-m-d',$r['cdate']).'</li>
                                        </ul>
                                    </div>
                                    <div class="clear" style="clear:both;"></div>
                                </li>';	
							 } 
							}
							?>
                           </ul></div>
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
                            <?php    foreach($questions as $row){  
							if($row['id']){
								 echo '<li> <div class="question_title">
            <h5 class="on">'.((isset($qstates[$row['id']]) && $qstates[$row['id']]>0)?'<div class="newans"><div class="nlf"></div><em>'.$qstates[$row['id']].'</em><div class="nrf"></div></div>':'').'<span>[编号:'.$row['id'].']</span> <a href="'.site_url('question/'.$row['id']).'">'.$row['title'].'</a>'.($row['new_answer']>0 && $row['fUid']==$uid?'<em>新</em>':'').'</h5>
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
							?> 
                                 
                            </ul>
                        </div>
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> 