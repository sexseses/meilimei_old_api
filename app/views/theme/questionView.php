<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css"><div class="page_content932">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('user/Fquestions'); ?>">已解答问题</a>><a  >查看详细</a></li>
                                
                            </ul>
                        </div>
                        <div class="question_detail q_colose">
                        	<h4>【问题】<?php echo $results[0]['title'] ?></h4>
                            <p><?php echo $results[0]['description']; if(!empty($attaches)) echo '<br><img src="http://pic.meilimei.com.cn/upload/'.$attaches[0]['savepath'].'"/>';?></p>
                           <h6><?php echo  date('Y-m-d',$results[0]['cdate']) ?>, <?php echo $diff ?> </h6>
                        </div>
                        <?php 
						foreach($answers as $row){
							echo '<div class="question_answered">
                        	<h5>用户:'.(substr($row['username'],0,3).'** ').'</h5>
                            <p>'.$row['content'].'</p>
                              <h6> 回答时间：'.date('Y-m-d',$row['cdate']).'</h6>
                        </div>';
						} 
						?>  
                        
                    </div>
                    <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div><script type="text/javascript"> 
					$(function (){
					  $(".question_nav ul li").click(function(){$(".question_nav ul li").removeClass();  $(this).addClass("on"); });
					});
</script>