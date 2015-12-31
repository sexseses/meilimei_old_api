<?php  if ($msg = $this->session->flashdata('flash_message')) {
	echo $msg; 
} ?> <script src="http://static.meilimei.com.cn/public/js/amCharts/amcharts.js" type="text/javascript"></script>
<script src="http://static.meilimei.com.cn/public/js/amCharts/raphael.js" type="text/javascript"></script>
<div class="page_content937">
  <div class="institutions_info new_institutions_info"> <?php  $this->load->view('manage/leftbar'); ?>
    <div class="manage_center_right">
                    	<div class="question_shortcuts">
                        	<ul>
                            	<li><a href="<?php echo site_url('manage/tongji')?>">统计管理</a></li><li><a href="<?php echo site_url('manage/tongji/fenduan')?>">分段统计</a></li>
                                <li><a href="<?php echo site_url('manage/tongji/fenduan2')?>">分段统计2</a></li>
                                <li><a href="<?php echo site_url('manage/tongji/online')?>">在线统计</a></li>
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                       <div class="manage_yuyue" > <div class="manage_search"><form method="get">
						<ul> <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li><li><button type="submit">查询</button></li></ul> </form></div>
                        	<div class="manage_yuyue_form">
       <script type="text/javascript">
           var chart;
           var chartData = [
		   <?php  foreach($res as $r){
			   echo '{
                       date: "'.$r['day'].'号",
                       visits: "'.$r['num'].'" 
                   },';			   
		   }
		   ?> 
	      ];

           AmCharts.ready(function () {
               // generate some random data first
            //   generateChartData();

               // SERIAL CHART
               chart = new AmCharts.AmSerialChart(); 
               chart.dataProvider = chartData;
               chart.categoryField = "date";

               // listen for "dataUpdated" event (fired when chart is inited) and call zoomChart method when it happens
           //    chart.addListener("dataUpdated", zoomChart);

               // AXES
               // category
               var categoryAxis = chart.categoryAxis;
               categoryAxis.parseDates = false; // as our data is date-based, we set parseDates to true
               //categoryAxis.minPeriod = "DD"; // our data is daily, so we set minPeriod to DD
               categoryAxis.minorGridEnabled = true;
               categoryAxis.axisColor = "#DADADA";

               // first value axis (on the left)
               var valueAxis1 = new AmCharts.ValueAxis();
               valueAxis1.axisColor = "#FF6600";
               valueAxis1.axisThickness = 2;
               valueAxis1.gridAlpha = 0;
               chart.addValueAxis(valueAxis1);

         

               // third value axis (on the left, detached)
               valueAxis3 = new AmCharts.ValueAxis();
               valueAxis3.offset = 50; // this line makes the axis to appear detached from plot area
               valueAxis3.gridAlpha = 0;
               valueAxis3.axisColor = "#B0DE09";
               valueAxis3.axisThickness = 2;
               chart.addValueAxis(valueAxis3);

               // GRAPHS
               // first graph
               var graph1 = new AmCharts.AmGraph();
               graph1.valueAxis = valueAxis1; // we have to indicate which value axis should be used
               graph1.title = "在线人数";
               graph1.valueField = "visits";
               graph1.bullet = "round";
               graph1.hideBulletsCount = 30;
               graph1.bulletBorderThickness = 1;
               chart.addGraph(graph1);
 
 

               // CURSOR
               var chartCursor = new AmCharts.ChartCursor();
               chartCursor.cursorPosition = "mouse";
               chart.addChartCursor(chartCursor);

               // SCROLLBAR
               var chartScrollbar = new AmCharts.ChartScrollbar();
               chart.addChartScrollbar(chartScrollbar);

               // LEGEND
               var legend = new AmCharts.AmLegend();
               legend.marginLeft = 110;
               legend.useGraphSettings = true;
               chart.addLegend(legend);

               // WRITE
               chart.write("newjigou");
           });

          
           function zoomChart() { 
               chart.zoomToIndexes(10, 20);
           }
        </script>
<h3>在线人数</h3><div id="newjigou" style="width:100%;height:350px;"></div>  
  
                            </div> 
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div><script> $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  })  </script>
</div>
