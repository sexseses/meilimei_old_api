
<html>
	<head>
		<title>验证码查询页面</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
<style type="text/css">
table{border-collapse:collapse;border-spacing:0;border-left:1px solid #888;border-top:1px solid #888;background:#efefef;}
th,td{border-right:1px solid #888;border-bottom:1px solid #888;padding:5px 15px;}
th{font-weight:bold;background:#ccc;}
</style>
	<body>
		<form action="logincode.php?act=query" method="post"  >
				<input type="text" name="mobile" id="mobile" value="<?php echo isset($_POST['mobile'])?$_POST['mobile']:''?>" />
				<input type="submit" value="查询" />
		</form>
		<table broder="1">
		  <thead>
		      <tr>
		          <td>手机</td>
		          <td>号码</td>
		          <td>是否有效</td>
		      </tr>
		  </thead>
		  <tbody>
		      <?php
		      if($_GET){
        			$act = $_GET['act'];
        			if($act == 'query'){
        				//$con = new mysqli("dbmeilimei.mysql.rds.aliyuncs.com","dbuser","1QAZWSX12_","meilimei");
        				$con = new mysqli("kingsley.mysql.rds.aliyuncs.com","kingsley","123123","meilimei");
        				$mobile = $_POST['mobile'];
        				if(empty($mobile)){
        				    echo "请填写手机号";
        				}else{
            				$sql = "select * from com_send_sms where mobile = '{$mobile}' order by sendtime DESC";
            				$rs = $con->query($sql);
            				while($row = $rs->fetch_array()){
            				    echo "<tr>";
            					echo "<td>$row[1]</td>";
            					echo "<td>$row[2]</td>";
            					if(time()-$row[3]>3600*48){
            					    echo "<td>已失效</td>";
            					}else{
            					    echo "<td>有效</td>";
            					}
            					echo "</tr>";
            					break;
            			     }
						}
        			}
		       }
		      ?>
		  </tbody>
		</table>
		
	</body>
</html>