<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">label{font-weight:bold;} li,dd{ margin:3px auto;padding:0px 10px;line-height:30px;height:auto; min-height:30px;  }  </style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="<?php echo site_url('manage') ?>">客户记录</a> 派单沟通</li><li style="float:right;"></li>
                                 <li style="float:right;">><a href="javascript:window.history.back(-3);">返回前页</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form"> 
                               <form method="post"><input type="hidden" value="<?php echo $user[0]->uid ?>" name="touid" /><input type="hidden" name="<?php echo $this->security->get_csrf_token_name()?>" value="<?php echo $this->security->get_csrf_hash() ?>" />
                                <textarea name="talks" style="width:500px;height:50px"></textarea>
                                <button type="submit" style="height:25px;width:100px;" name="submit">发送</button>
                               </form>
                            	</div>  
                                <div class="clear" style="clear:both;"></div>
                                <dl>
                                <?php foreach($talk as $r){
									echo '<dd class="clear"><label>'.($user[0]->uid==$r->fuid?$user[0]->alias:'我').': </label>'.$r->message.' - 日期:'.date('Y-m-d H:i:s',$r->cdate).'</dd>';
								}
								?>
                                </dl><div class="clear" style="clear:both;"></div>
                            </div> 
                        </div>
                    </div>  
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
