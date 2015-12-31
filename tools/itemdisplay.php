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
                  <td>id</td>
                  <td>name</td>
                  <td>oldname</td>
                  <td>修改</td>
              </tr>
          </thead>
          <tbody>
            <?php
                $con = new mysqli("10.10.10.5","root","meilimei","mlm_test");
                $con->query("set names UTF8");
                $sql = "SELECT * FROM new_items WHERE 1=1";
                $rs = $con->query($sql);
                while($row = $rs->fetch_array()){
                    echo "<tr>";
                    echo "<td>$row[0]</td>";
                    echo "<td>$row[2]</td>";
                    echo "<td>$row[3]</td>";
                    echo "<td><a href ='itemedit.php?id=$row[0]'>点击</a></td>";
                    echo "</tr>";
                }
            ?>
          </tbody>
        </table>
<?php
$con->free();
?>  
    </body>
</html>