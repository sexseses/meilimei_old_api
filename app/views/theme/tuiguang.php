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
                    
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="#">推广管理</a></li>
                            </ul>
                        </div>
                        <div class="doctor_promotion" >
                        	<h5><?php echo $tuiguanginfo ?></h5>
                        	<div class="doctor_promotion_left"><?php echo form_open('counselor/submittuiguang', array('id'=>'calender_form')); ?>
                            	<ul>
                                	<li>置顶信息： <?PHP echo $info['name'] ?> </li>
                                    <li>置顶城市：<select name="city">
                                    <option value="北京" <?PHP echo $info['city']=='北京'?'selected="selected"':'' ?>>北京</option>
                                    <option value="上海" <?PHP echo $info['city']=='上海'?'selected="selected"':'' ?>>上海</option>
                                    <option value="广州" <?PHP echo $info['city']=='广州'?'selected="selected"':'' ?>>广州</option>
                                    <option value="深圳" <?PHP echo $info['city']=='深圳'?'selected="selected"':'' ?>>深圳</option>
                                    <option value="杭州" <?PHP echo $info['city']=='杭州'?'selected="selected"':'' ?>>杭州</option>
                                     </select></li> 
                                    <li>置顶时间 (按住CTRL键可以多选，按住Shift键可以连续选择时间段)<div id="J_datePicker"></div><br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;共<span id="totalday">0</span>天,  总计：<span id="totalfee">0</span>元 </li><input type="hidden" name="selectdate" value="" id="selectdate" />
                                
                                    <li><input name="submit" type="submit" value="立即购买" class="buy_quickly"></li>
                                    <li>注：我已仔细阅读并同意<a href="#">美丽诊所用户协议</a>若有违反，美丽诊所不予退费</li>
                                   
                                </ul>
                            </div>
                      
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