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
                            </ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > <div class="manage_search"><form method="get">
						<ul> <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li><li><button type="submit">查询</button></li></ul> </form></div>
                        	<div class="manage_yuyue_form">
       <script type="text/javascript">
           var chart;
           var chartData = [
		   <?php  foreach($newJg as $r){
			   echo '{
                       date: "'.$r['day'].'号",
                       visits: "'.$r['jg'].'",
                       hits: "'.$r['ys'].'",
                       views:"'.$r['us'].'"
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
               graph1.title = "机构";
               graph1.valueField = "visits";
               graph1.bullet = "round";
               graph1.hideBulletsCount = 30;
               graph1.bulletBorderThickness = 1;
               chart.addGraph(graph1);

               // second graph
               var graph2 = new AmCharts.AmGraph();
               graph2.valueAxis = valueAxis1; // we have to indicate which value axis should be used
               graph2.title = "专家";
               graph2.valueField = "hits";
               graph2.bullet = "square";
               graph2.hideBulletsCount = 30;
               graph2.bulletBorderThickness = 1;
               chart.addGraph(graph2);

               // third graph
               var graph3 = new AmCharts.AmGraph();
               graph3.valueAxis = valueAxis1; // we have to indicate which value axis should be used
               graph3.valueField = "views";
               graph3.title = "用户";
               graph3.bullet = "triangleUp";
               graph3.hideBulletsCount = 30;
               graph3.bulletBorderThickness = 1;
               chart.addGraph(graph3);

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
<h3>新加入专家数与新加入机构数</h3><div id="newjigou" style="width:100%;height:350px;"></div>  

<script type="text/javascript">
            var chart2; 
            var chartData2 = [ 
			<?php
			 foreach($sumQ as $r){
				echo '{
                    "tag": "'.$r['day'].'",
                    "tt": "'.$r['has'].'", 
					"fb": "'.$r['no'].'", 
                }, ';
			 }
			?>    
            ];
            
            
            AmCharts.ready(function () {
                // SERIAL CHART
                chart2 = new AmCharts.AmSerialChart();
                chart2.dataProvider = chartData2;
                chart2.categoryField = "tag";
                chart2.startDuration = 0.5;
                chart2.balloon.color = "#000000"; 
            
                // AXES
                // category
                var categoryAxis = chart.categoryAxis;
                categoryAxis.fillAlpha = 1;
                categoryAxis.fillColor = "#FAFAFA";
                categoryAxis.gridAlpha = 0;
                categoryAxis.axisAlpha = 0;
                categoryAxis.gridPosition = "start";
                categoryAxis.position = "top";
            
                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.title = "分布统计";
                valueAxis.dashLength = 5;
                valueAxis.axisAlpha = 0; 
                valueAxis.integersOnly = true;
                valueAxis.gridCount = 10;
                valueAxis.reversed = false; // this line makes the value axis reversed
                chart2.addValueAxis(valueAxis);
            
              
            
                // Germany graph
                var graph = new AmCharts.AmGraph();
                graph.title = "未回答";
			    graph.type = "column";
				graph.lineAlpha = 0;
               
                graph.fillAlphas = 0.8; 
                graph.valueField = "tt";
                graph.balloonText = "  [[value]]";
            //    graph.bullet = "round"; 
                chart2.addGraph(graph);
            
                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
                graph.title = "已回答";
                graph.valueField = "fb";
                graph.balloonText = "  [[value]]";
                graph.bullet = "round";
                chart2.addGraph(graph);
                
                // CURSOR
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.cursorPosition = "mouse";
                chartCursor.zoomable = false;
                chartCursor.cursorAlpha = 0;
                chart2.addChartCursor(chartCursor);                
            
                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart2.addLegend(legend);
            
                // WRITE
                chart2.write("chartdiv2");
            });
        </script><h3>未回答和已回答的咨询数</h3><div id="chartdiv2" style="width:100%;height:350px;"></div>  
                                <div class="clear" style="clear:both;"></div>
                            </div> 
                        </div> 
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div><script> $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  })  </script>
</div>
