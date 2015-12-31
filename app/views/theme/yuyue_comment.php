<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>预约备注</title>
</head>
<body>
<?php  
$attributes = array( 'id' => 'commentarea');
echo form_open('counselor/submitBeizhu'); ?> 
<textarea id="comments" name="comments" style="height:60px; width:300px"></textarea>
<?php
$result['paramname'] =  $this->security->get_csrf_token_name();
         $result['paramval']  =  $this->security->get_csrf_hash();
        echo '<input type="hidden" id="'.$result['paramname'].'" name="'.$result['paramname'].'" value="'.$result['paramval'].'"/><input id="dataid" type="hidden" name="dataid" value="'.$this->input->get('dataid').'"/>';
?><br><a class="submitbeizhu">提交</a>
</form>
</body>
</html>
