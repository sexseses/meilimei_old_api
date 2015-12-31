<html>
	<head>
		<title>活动查询页面</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>
<style type="text/css">
table{border-collapse:collapse;border-spacing:0;border-left:1px solid #888;border-top:1px solid #888;background:#efefef;}
th,td{border-right:1px solid #888;border-bottom:1px solid #888;padding:5px 15px;}
th{font-weight:bold;background:#ccc;}
</style>
	<body>	
		<table broder="1">
		  <thead>
		      <tr>
		          <td>姓名</td>
		          <td>手机号码</td>
		          <td>城市</td>
		          <td>时间</td>
		          <td>网站</td>
		      </tr>
		  </thead>
		  <tbody>
		      <?php
        				//$con = new mysqli("dbmeilimei.mysql.rds.aliyuncs.com","dbuser","1QAZWSX12_","meilimei");
		             
        				$con = new mysqli("kingsley.mysql.rds.aliyuncs.com","kingsley","123123","mlm_event");
        				 $con->query("set names uft8");
            				$sql = "SELECT * FROM general_activity WHERE 1=1";
            			
            				$rs = $con->query($sql);
            				
            				while($row = $rs->fetch_array()){
            				    echo "<tr>";
            					echo "<td>$row[1]</td>";
            					echo "<td>$row[2]</td>";
            					echo "<td>$row[3]</td>";
            					echo "<td>$row[4]</td>";
            					echo "<td>$row[5]</td>";
            					echo "</tr>";
            					
            			     }
		      ?>
		  </tbody>
		</table>
		
	</body>
</html>