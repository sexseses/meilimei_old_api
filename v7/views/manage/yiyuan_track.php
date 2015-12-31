<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">tr{ margin:3px auto;line-height:30px;height:30px; text-align:center}  </style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="<?php echo site_url('manage/yiyuan') ?>">机构</a>派单记录</li><li style="float:right;"></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form"> 
                              <table><tr><th width="100">ID </th><th width="150">姓名 </th><th width="150">手机 </th><th width="100">重单</th><th width="150">联系状态</th> <th width="150">派单时间</th><th width="150">我的备注</th><th width="80">交谈</th><th width="400">医院备注</th></tr>
							   <?php
							   $states = array('待联系','已联系','无法联系','已到院未手术','已到院手术');
							   $i = 1;
							   foreach($res as $r){
								   $query = $this->db->get_where('yuyueTrack', array('sn' =>$r['sn'],'uid' =>$r['uid']))->result();
								   $ttml = '<ul>';
								   $p = 0;
								   foreach($query as $t){$p++;
									$ttml .='<li>['.$p.'] '.$t->remark.''.date('Y-m-d H:i:s',$t->cdate).'</li>';   
								   } 
								   $ttml .='</ul>';  
								   $cd = $r['chongdan']==0?'否':'是<img width="200" height="150" src="'.site_url().$r['linkpic'].'">';
								   echo '<tr><td>  '.$r['userby'].' </td><td>  '.$r['name'].' </td><td>  '.$r['phone'].' </td><td>'.$cd.'</td><td> '.$states[$r['contactState']].' </td><td> '.date('Y-m-d H:i:s',$r['cdate']).' </td><td>'.$r['sendremark'].'</td><td><a href="'.site_url('manage/home/paidan_talk/'.$r['id']).'">交谈</a></td><td>'.$ttml.'</tr>'; 
							   }							   
							   ?>
                               </table>
                            	</div> 
                                 <?php  
                                function DS($res,$city){
									$html = '';
									foreach($res as $r){
                                      $r['city']==$city&&$html .='<option value="'.$r['userid'].'">'.$r['name'].'</option>';
                                    }
									return $html;
								} ?>
                                <div class="clear" style="clear:both;"></div>
                            </div> 
                        </div>
                    </div>  
    <div class="clear" style="clear:both;"></div>
  </div>
</div>
