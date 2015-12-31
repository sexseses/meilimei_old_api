<?php
$con = new mysqli("10.10.10.5","root","meilimei","mlm_test");
$con->query("set names UTF8");
$id = "";
    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }else{
        $id = $_POST['id'];
    }
    if(empty($id)){
      echo "id 木有！";die;
    }
?>
<html>
    <head>
        <title>修改页面</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    </head>
<style type="text/css">
table{border-collapse:collapse;border-spacing:0;border-left:1px solid #888;border-top:1px solid #888;background:#efefef;}
th,td{border-right:1px solid #888;border-bottom:1px solid #888;padding:5px 15px;}
th{font-weight:bold;background:#ccc;}
</style>
<?php
if(isset($_GET['act'])){
  if(isset($_POST['item_name'])){
      $item_name = $_POST['item_name'];
      $sql = "update new_items set name = '$item_name' where id = $id";
      //echo $sql;
      $rs = $con->query($sql);
      if($rs){
          echo "<meta http-equiv='refresh' content='3; URL=http://10.10.10.5/tools/itemdisplay.php' charset=utf-8 />";
          echo "<br/><h3 align='center'><a href='http://10.10.10.5/tools/itemdisplay.php'>更新成功系统将自动在3秒后跳转，如果没跳请点击该链接</a></h3>";
          exit;
      }
  }
  
}
?>
    <body>  
        <table broder="1">
           
          <tbody>
             <form action="itemedit.php?act=edit" method="post">
              <input type="hidden" name="id" value="<?php echo $id ?>">
                <input type="text" name="item_name" value="" />  
                <input type="submit" value="提交">             
             </form>
          </tbody>
        </table>
<?php
$con->free();
?>  
    </body>
</html>