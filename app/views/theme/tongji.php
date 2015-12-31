<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" ><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script><div class="page_content933">
            	<div class="institutions_info">
                	<?php $this->load->view('theme/include/dashboard'); ?>
                    <div class="Personal_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('counselor/tuijian') ?>">推荐信息</a></li><li class="selected"><a href="#">统计报表</a></li>
                            </ul>
                        </div><style type="text/css"> li{float:left;padding:3px;}</style>
                        <div style="padding:10px 20px"> 
                        	<ul style="border-bottom:dashed 1px #CCCCCC"> 
                                    <li style="width:200px">系统</li>
                                    <li style="width:200px">IP</li>
                                    <li style="width:200px">时间</li>  
                                    <div class="clear" style="clear:both;"></div>
                                </ul>
                                <ul> 
                                <?php
								foreach($results as $row){
									echo '<li style="width:200px">'.(trim($row->systype)!=''?str_replace('rv:18.0) Ge','Firefox',$row->systype):'未知') .'</li>
                                    <li style="width:200px">'.$row->ip .'</li>
                                    <li style="width:200px">'.date('Y-m-d',$row->cdate).'</li>  
                                    <div class="clear" style="clear:both;"></div>';
								}
								?>
                                    
                                </ul>
                        </div><div class="paging">
                                <div class="paging_right">
                                    <ul style=" width:125px;">
                                        <li ><a href="<?php echo $preview ?>" class="preview">&nbsp;</a></li>
                                        <li  ><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                    </ul>
                                    <h5>第<?php echo $offset ?>-<?php echo $offset+count($results)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                                </div>
                            </div>
                    </div> <div class="clear" style="clear:both;"></div>
                </div>
            </div>
		</div> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/ZeroClipboard.min.js"></script><script type="text/javascript" src="http://static.meilimei.com.cn/public/js/mains.js"></script>