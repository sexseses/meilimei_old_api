<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <script charset="utf-8" src="<?php echo base_url()?>/editor/kindeditor.js"></script>
<script charset="utf-8" src="<?php echo base_url()?>/editor/lang/zh_CN.js"></script>
<script>
        KindEditor.ready(function(K) {
                window.editor = K.create('.editer');
        });
</script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo base_url('manage/category/')?>">项目管理</a></li><li class="on"><a>添加项目</a></li>
                            </ul>
                        </div> <style type="text/css">dd label{display:inline-block;padding:0px 5px;} dd{display:block;line-height:30px;height:30px;margin-bottom:5px;}</style>
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > 
                        	<div class="manage_yuyue_form"><?php echo form_open_multipart(); ?>
                            	<dl>
                                <dd><label>名称</label><input type="text" value="<?php echo $results[0]->name ?>" name="name" /></dd>
                                <dd><label>价格</label><input type="text" value="<?php echo $results[0]->price ?>" name="price" /></dd>
                                <dd style="height:60px"><label>描述</label><textarea style="height:50px;width:300px;" name="des"><?php echo $results[0]->des ?></textarea></dd>
                                <dd><label>栏目图</label><input type="file" name="surl" />  90*90</dd>
                                <dd style="height:90px;"><img width="90" height="90" src="<?php echo site_url().'upload/'.$results[0]->surl ?>" /></dd>
                                <dd><label>缩略图</label><input type="file" name="burl" /> 130*130</dd><input type="hidden" name="ssurl" value="<?php echo $results[0]->surl ?>" />
                                <input type="hidden" name="sburl" value="<?php echo $results[0]->burl ?>" />
                                 <dd style="height:130px;"><img width="130" height="130" src="<?php echo site_url().'upload/'.$results[0]->burl ?>" /></dd>
                                 
                                <dd><label>位置</label><select name="position"><option  value="0">Top</option>
                                <?php foreach($clists as $r){ 
									echo '<option '.($r['id']==$results[0]->pid?'selected="selected"':'').' value="'.$r['id'].'">'.$r['name'].'</option>';
								}
								?> 
                                </select></dd>
                                <dd><label>关注度</label>
                                    <input type="text" name="attention" value="<?php echo $results[0]->attention;?>"/>
                                </dd>
                                <dd><label>满意度</label>
                                    <input type="text" name="satisfaction" value="<?php echo $results[0]->satisfaction;?>"/>

                                </dd>
                                <dd><label>安全度</label>
                                    <input type="text" name="safety" value="<?php echo $results[0]->safety;?>"/>

                                </dd>
                                <dd><label>复杂度</label> 
                                <select name="complexity">
                                <option <?php echo $results[0]->complexity==1?'selected="selected"':'' ?> value="1">1</option>
                                <option <?php echo $results[0]->complexity==2?'selected="selected"':'' ?> value="2">2</option>
                                <option <?php echo $results[0]->complexity==3?'selected="selected"':'' ?> value="3">3</option>
                                <option <?php echo $results[0]->complexity==4?'selected="selected"':'' ?> value="4">4</option>
                                <option <?php echo $results[0]->complexity==5?'selected="selected"':'' ?> value="5">5</option>
                                </select>
                                </dd>
                                <dd><label>治疗手段</label><input type="text" value="<?php echo $results[0]->treatment ?>" name="treatment" /></dd>
                                <dd><label>效果持续</label><input type="text" value="<?php echo $results[0]->tlasts ?>" name="tlasts" /></dd>
                                <dd><label>恢复时间</label><input type="text" value="<?php echo $results[0]->recovery_time ?>" name="recovery_time" /></dd>
                                <dd style="height:200px"><label>治疗方法</label><textarea  class="editer" style="height:120px;width:700px" type="text" name="DStreatments" /><?php echo $results[0]->DStreatments ?></textarea></dd>                 
                                <dd style="height:200px"><label>治疗效果</label><textarea  class="editer"  style="height:120px;width:700px" type="text" name="XGtreatment" /><?php echo $results[0]->XGtreatment ?></textarea></dd>                 
                                <dd style="height:60px"><label>适合人群</label><textarea  style="height:60px;width:300px" type="text" name="crowd" /><?php echo $results[0]->crowd ?></textarea></dd>                  
                                <dd style="height:60px"><label>恢复过程</label><textarea  style="height:60px;width:300px" type="text" name="recovery_process" /><?php echo $results[0]->recovery_process ?></textarea></dd>              
                                <dd style="height:60px"><label>注意事项</label><textarea  style="height:60px;width:300px" type="text" name="notice" /><?php echo $results[0]->notice ?></textarea></dd>
                                <dd style="height:60px"><label>优点</label><textarea  style="height:60px;width:300px" type="text" name="advantage" /><?php echo $results[0]->advantage ?></textarea></dd>
                                <dd style="height:60px"><label>缺点</label><textarea  style="height:60px;width:300px" type="text" name="shortcomings" /><?php echo $results[0]->shortcomings ?></textarea></dd> 
                                <dd style="height:60px"><label>风险</label><textarea  style="height:60px;width:300px" type="text" name="risk" /><?php echo $results[0]->risk ?></textarea></dd>                             <dd><label>治疗次数</label><input type="text" value="<?php echo $results[0]->treatment_time ?>" name="treatment_time" /></dd>
                                <dd><label>热门</label><input type="checkbox" <?php echo $results[0]->is_hot?'checked="checked"':''; ?> value="1" name="is_hot" /></dd>
                                <dd><label>首页默认项目</label><input type="checkbox" <?php echo $results[0]->is_default?'checked="checked"':''; ?> value="1" name="is_default" /></dd>
                                <dd><label>搜索热门</label><input type="checkbox" <?php echo $results[0]->type&1?'checked="checked"':''; ?> value="1" name="type[]" /></dd>
                                <dd><label>瀑布流热门</label><input type="checkbox" <?php echo $results[0]->type&2?'checked="checked"':''; ?> value="2" name="type[]" /></dd>
                                <dd><label>APP类别</label><input type="checkbox" <?php echo $results[0]->app==1?'checked="checked"':''; ?> value="1" name="app" /></dd>
                                <dd><label>排行榜</label><input type="checkbox" <?php echo $results[0]->isrecommend==1?'checked="checked"':''; ?> value="1" name="isrecommend" /></dd>
                                <dd><label>排序</label><input type="text" value="<?php echo $results[0]->order ?>" name="order" /></dd>
                                    <dd><label>分类级别</label><input type="text" value="<?php echo $results[0]->other ?>" name="other" /></dd>
                                <dd><button style="width:100px" type="submit">更新</button></dd>
                                </dl></form>
                                <div class="clear" style="clear:both;"></div>
                                
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div>
</div>
