<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" >
<script src="http://static.meilimei.com.cn/public/js/calendar/kit.js"></script><script src="http://static.meilimei.com.cn/public/js/wen.js"></script>
		<!--[if IE]>
		<script src="http://static.meilimei.com.cn/public/js/calendar/ieFix.js"></script>
		<![endif]--> 
		<script src="http://static.meilimei.com.cn/public/js/calendar/array.js"></script>
		<script src="http://static.meilimei.com.cn/public/js/calendar/date.js"></script>
		<script src="http://static.meilimei.com.cn/public/js/calendar/dom.js"></script>
		<script src="http://static.meilimei.com.cn/public/js/calendar/selector.js"></script> 
		<!--widget-->
		<script src="http://static.meilimei.com.cn/public/js/calendar/datepicker.js"></script>
		<script src="http://static.meilimei.com.cn/public/js/calendar/datepicker-n-months.js"></script>
		<link rel="stylesheet" href="http://static.meilimei.com.cn/public/js/calendar/datepicker.css" />
<div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_nav">
                        	<ul>
            <li<?PHP echo $this->uri->segment(2) == 'topic'?' class="on"':'' ?>><a href="<?php echo site_url('user/topic') ?>" class="to_answer">全部话题</a></li>
            
          <li<?PHP echo $this->uri->segment(2) == 'topicjoin'?' class="on"':'' ?>><a href="<?php echo site_url('user/topicjoin') ?>" class="resolved">参与的话题</a></li>
                            </ul>
                        </div>
                      <div class="paging">
                                <div class="paging_right">
                            	<ul>
                                    <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                                    <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                </ul>
                                <h5>第<?php echo $offset ?>-<?php echo $offset+count($topics)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                            </div>
                            </div>
                        <div class="question_list">
                        	<ul>
                            <?php
							foreach($topics as $row){
								$tmp=unserialize($row['type_data']);
								echo '<li>
                                    <div class="question_title">
                                        <h5><a href="#">'.$tmp['title'].'</a></h5>
                                        <p>'.$row['content'].'</p>
                                    </div>
                                    <div class="question_state">'.($row['ctime']>time()-600?'新消息':'').'</div>
                                    <div class="question_time">
                                    	<ul>
                                        	<li>更新时间：'.date('Y-m-d',$row['newtime']).'</li>
                                            <li>提问时间：'.date('Y-m-d',$row['ctime']).'</li>
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
		</div> <script type="text/javascript"> 
	 
			$kit.$(function() {
				//默认日历
				window.picker = new $kit.ui.DatePicker.NMonths();
				picker.init();
				$kit.el('#J_datePicker').appendChild(picker.picker);
				picker.show();
				picker.ev({
					ev : 'change',
					fn : function(e) {$("#selectdate").val(picker.getValue());wen.baseinfo.url="<?php echo site_url() ?>"; wen.tuiguangfee(picker.getValue());
					}
				})
				 
				$kit.ev({
					el : document,
					ev : 'click',
					fn : function(e) {
						var input = $kit.el('#J_input');
						d = input[$kit.ui.DatePicker.defaultConfig.kitWidgetName];
						if(d && !$kit.contains(d.picker, e.target) && input != e.target) {
							d.hide();
						}
					}
				});
			})
		</script>