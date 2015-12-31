<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/personal_center.css" >
<link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" >

<script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script>
<div class="page_content933">
            	<div class="institutions_info">
                	<?php  $this->load->view('manage/leftbar'); ?>
                    <div class="Personal_center_right">
                    <div class="question_nav">
                        <ul>
                            <li class="on"><a href="<?php echo base_url('manage/coupon_card')?>">优惠券管理</a></li>
                            <li><a href="<?php echo base_url('manage/coupon_card_add')?>">添加优惠券</a></li>
                        </ul>
                    </div>
                    <div class="manage_yuyue" >
                       <div class="manage_search">
								<form  method="get" action="<?php echo site_url('coupon_card/search') ?>">
                            	<ul> 
                                    <li>序号<input name="sn" value="<?php  echo $this->input->get('name'); ?>" type="text"></li> 
                                    <li>批次<input name="phone" type="text"></li>
                                    <li><input name="" type="submit" value="搜索" class="search"></li>
                                </ul>
                                </form>
                            	</div>
                        	<div class="yuyue_form">
                            <ul class="new_ul">
                                
                                <li style="width:15%;">批次</li>
							    <li style="width:15%;">开始时间</li>
                                <li style="width:15%;">结束时间</li>
	                            <li style="width:15%;">消费金额</li>
	                            <li style="width:15%;">限制金额</li> 
	                            <li style="width:10%; ">操作</li>
                            </ul>
                            <?php 
								foreach($coupon_rs as $row){
                            ?>  
                                    <ul >
                                    <li class="Vertical01" style="width:15%;"><?php echo $row['batch']?></li>
                                    <li class="Vertical01" style="width:15%;"><?php echo date('Y-m-d H:i:s',$row['begin'])?></li> 
                                	<li class="Vertical01" style="width:15%;"><?php echo date('Y-m-d H:i:s',$row['end'])?></li>
                                    <li class="Vertical01" style="width:15%;"><?php echo date('Y-m-d H:i:s',$row['credit'])?></li>
                                    <li class="Vertical01" style="width:15%;"><?php echo date('Y-m-d H:i:s',$row['quota'])?></li>
                                    <li class="<?php echo $row['is_view']?'is_new':'' ?>" style="width:10%;float:right;"><a class="<?php echo site_url('counselor/danview/'.$row['id']) ?>" href="<?php echo site_url('counselor/danview/'.$row['id'])?>">查看</a></li>
                                </ul> <div class="clear" style="clear:both;"></div>                                
									
							<?php 
                                }
                            ?>
								

                                <div class="clear" style="clear:both;"></div>

                            </div>
                            <div class="paging">
                                <div class="paging_right">
                            	<ul>
                                    <li><a href="<?php echo $preview?>" class="preview">&nbsp;</a></li>
                                    <li><a href="<?php echo $next ?>" class="next">&nbsp;</a></li>
                                </ul>
                                <h5>第<?php echo $offset ?>-<?php echo $offset+count($data)-1 ?>个，共<?php echo $total_rows ?>个</h5>
                            </div>
                            </div>
                        </div>
                    </div>     
                    <div class="clear" style="clear:both;"></div>
                    <div id="dialog" title="Basic dialog" style="display:none">
                        <form action="<?php echo site_url('counselor/submitNewBeizhu')?>" method="post" class="beizhu">
                            <input type="text" name="comments" >
                            <input type="hidden" id="<?php echo $result['paramname']?>" name="<?php echo $this->security->csrf_token_name?>" value="<?php echo $this->security->csrf_hash?>" /> 
                        </form>
                    </div>
                </div>
            </div>
		</div> 