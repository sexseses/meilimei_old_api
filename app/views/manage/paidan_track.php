<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?>  <link rel="stylesheet" type="text/css" href="http://static.meilimei.com.cn/public/css/thickbox.css" > <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/wen.js"></script> <script type="text/javascript" src="http://static.meilimei.com.cn/public/js/jquery.cityselect.js"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right"><style type="text/css">tr{ font-size:12px; margin:3px auto;line-height:30px;height:30px; text-align:center}  </style>
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li style="float:left;"><a href="<?php echo site_url('manage') ?>">客户记录</a> 派单跟踪</li> 
                                <li style="float:right;">><a href="javascript:window.history.back(-1);">返回前页</a></li>
                            </ul>
                        </div>
                        <div class="manage_yuyue" >
                        	<div class="manage_yuyue_form"> 
                              <table><tr><th width="8%">管理员</th><th width="10%">医院</th><th width="8%">联系状态</th><th width="12%">重单</th> <th width="10%">时间</th><th width="10%">派单备注</th><th width="5%">交谈</th><th width="30%">医院跟踪记录</th><th width="15%">操作</th></tr>
							   <?php
							   $states = array('待联系','已联系','无法联系','已到院未手术','已到院手术');
							   $i = 1;
							   foreach($res as $r){
								   $tmp = $this->db->get_where('users', array('id' => $r['fuid']))->result_array();
								   $yuyueTrack = $this->db->get_where('yuyueTrack', array('sn' =>$r['sn'],'uid' =>$r['uid']))->result();
								   $yuyueTalk = $this->db->get_where('yuyueTalk', array('talkid'=>$r['id']))->result();;
								   $yuyueTalkhtml = '';  
								   foreach($yuyueTalk as $j){ 
									   $yuyueTalkhtml.= ($j->fuid==$r['uid']?'【'.$r['alias'].'】 ':'【客服】 ').$j->message.'--'.date('Y-m-d H:i',$j->cdate).'<br>';
								   }
								   $ttml = '<ul>';
								   $p = 0;
								   foreach($yuyueTrack as $t){$p++;
									$ttml .='<li>['.$p.'] '.$t->remark.''.date('Y-m-d H:i:s',$t->cdate).'</li>';   
								   } 
								   $ttml .='</ul>';  
								   $cd = $r['chongdan']==0?'否':'是<a target="_blank" href="'.site_url().$r['linkpic'].'"><img width="200" height="150" src="'.site_url().$r['linkpic'].'"></a>';
								   echo '<tr><td>'.$tmp[0]['alias'].'<td><a href="'.site_url('manage/yiyuan/detail/'.$r['uid']).'">'.$r['alias'].'</a></td><td> '.$states[$r['contactState']].' </td><td>'.$cd.'</td><td> '.date('Y-m-d H:i:s',$r['cdate']).' </td><td>'.$r['sendremark'].'</td><td><a href="'.site_url('manage/home/paidan_talk/'.$r['id']).'">交谈</a></td><td>'.$ttml.'</td><td><a onclick="return confirm(\'删除 '.$r['alias'].'?\');"  href="'.site_url('manage/home/paidan_del/'.$r['id']).'">删除</a></tr>
								   <tr style="background:#efefef;text-align:left"><td colspan="9">'.($yuyueTalkhtml==''?'无回复':$yuyueTalkhtml).'</td></tr>'; 
								   
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
