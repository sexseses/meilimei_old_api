<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> 
<script src="http://static.meilimei.com.cn/public/js/amCharts/amcharts.js" type="text/javascript"></script>
<script src="http://static.meilimei.com.cn/public/js/amCharts/raphael.js" type="text/javascript"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_nav">
                        	<ul>
                            	<li><a href="<?php echo  site_url('manage/users'); ?>">普通用户管理</a></li>
                                <li><a href="<?php  echo site_url('manage/users/today'); ?>" >今日联系</a></li><li><a href="<?php echo site_url('manage/users/sendsms'); ?>">短信通知</a></li><li class="on"><a href="<?php echo site_url('manage/users/total'); ?>">统计</a></li></ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" ><div class="manage_search"><form method="get">
						<ul> <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li><li><button type="submit">查询</button></li></ul> </form></div>
           
            <script type="text/javascript">  $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  })  
            var chart; 
            var chartData = [
			  <?php  
			  foreach($res as $r){
				  echo '{
                    "uname": "'.$r['alias'].'",
                    "income": '.$r['num'].',
                    "expenses":'.$r['uid'].'
                },';
			  }?>  
			   {   "uname": "",
                    "income": 0,
                    "expenses":0
                } 
            ];


            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "uname";
                chart.startDuration = 1;
                chart.rotate = false;

                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.gridPosition = "start";
                categoryAxis.axisColor = "#DADADA";
                categoryAxis.dashLength = 3;

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.dashLength = 3;
                valueAxis.axisAlpha = 0.2;
                valueAxis.position = "bottom";
                valueAxis.title = "当日统计";
                valueAxis.minorGridEnabled = true;
                valueAxis.minorGridAlpha = 0.08;
                valueAxis.gridAlpha = 0.15;
                chart.addValueAxis(valueAxis);

                // GRAPHS
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "column";
                graph1.title = "联系用户数";
                graph1.valueField = "income";
                graph1.lineAlpha = 0;
                graph1.fillColors = "#66A330";
                graph1.fillAlphas = 0.8; 
                graph1.balloonText = "[[uname]] [[title]] : [[value]] ";
                chart.addGraph(graph1);

               
                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                chart.creditsPosition = "top-right";

                // WRITE
                chart.write("chartdiv");
            });    	         
 
</script>

         
        <div id="chartdiv" style="width:800px; height: 500px;"></div>
          </div> 
       </div>
    <div class="clear" style="clear:both;"></div>  
  </div> 
</div>
