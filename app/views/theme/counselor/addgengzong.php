<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    <style type="text/css">#track li,dd{display:block;width:100%;margin:10px auto;line-height:30px;height:30px;}#track li{margin:auto;text-align:left;} dd label{display:inline-block;width:100px;}</style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('counselor/yuyue') ?>">客户记录</a> 跟踪记录</li>
                            	 
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="yuyue_form">
                     <form method="post"><dl>
                             <dd><label>备注</label><textarea name="remark" cols="60" rows="2"></textarea></dd>
                             <dd><button type="submit" name="submit" style="width:90px;height:26px;">添加</button>
                             </dl> 
                              </form>
                                <div class="clear" style="clear:both;"></div>
                             <ul id="track">
                             <?php 
							 $i = 1;
							 foreach($res as $r){
								   echo '<li> ('.$i.') '.$r['remark'].' - 日期:'.date('Y-m-d H:i:s',$r['cdate']).'</li>';
								   $i++;
							 }
							 ?>
                             </ul><div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div> 
                    <div class="clear" style="clear:both;"></div>
                     
                </div>
            </div>
		</div> 