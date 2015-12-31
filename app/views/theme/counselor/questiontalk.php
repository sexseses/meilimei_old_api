<div class="dialogue_details" id="dialogue_<?php echo $this->input->get('talk_id') ?>">
  <ul>
  <?php  
  foreach($talks as $row){ 
	  echo '<li>
      <h5> '.($row['fuid']==$uid?'<em class="talkto">我： </em>':'<em class="talkto">对方：</em>').'</h5>
      <p>'.$row['content'].($row['haspic']==1?'<img src="'.$row['pic'].'">':'').'</p>
      <h6>'.$row['cTime'].'</h6>
    </li>';
  }?> 
    
    <li class="button"><?php echo form_open("counselor/talk",array('id' => 'replytalk')) ?>
      <textarea name="talkconent" cols="" rows="" class="write_box"></textarea><input type="hidden" name="talk_id" value="<?php echo $this->input->get('talk_id') ?>"/><input type="hidden" name="data_id" value="<?php echo $this->input->get('data_id') ?>"/>
      <div class="emotion"> <input type="hidden" name="vtokens" value="<?php echo md5(($this->input->get('talk_id')+$this->input->get('data_id'))*2); ?>" />
        <input name="" type="submit" id="buttonreply" class="button_answer" value="回复">
      </div>
      <div class="clear" style="clear:both;"></div>
      </form>
    </li>
  </ul>
</div>
