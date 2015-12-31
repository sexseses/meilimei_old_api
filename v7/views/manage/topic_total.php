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
                            	<li><a href="<?php echo site_url('manage/topic'); ?>">话题管理</a></li>
                                <li><a href="<?php echo site_url('manage/topic/add'); ?>">添加话题</a></li>
                                <li><a href="<?php echo site_url('manage/topic/nocla'); ?>">未分类</a></li>
                                <li><a href="<?php echo site_url('manage/topic/order'); ?>">推荐排序</a></li>
                                <li><a href="<?php echo site_url('manage/topic/setting'); ?>">话题配置</a></li>
                                <li><a href="<?php echo site_url('manage/topic/comments'); ?>">评论</a></li>
                                 <li class="on"><a href="<?php echo site_url('manage/topic/total'); ?>">统计</a></li>
                            </ul>
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
                    "uname": "'.$r['name'].'",
                    "income": '.$r['topic'].',
                    "expenses":'.$r['reply'].'
                },';
			  }?>  
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
                graph1.title = "新帖";
                graph1.valueField = "income";
                graph1.lineAlpha = 0;
                graph1.fillColors = "#66A330";
                graph1.fillAlphas = 0.8; 
                graph1.balloonText = "[[uname]] [[title]] : [[value]] ";
                chart.addGraph(graph1);

                // line graph
                var graph2 = new AmCharts.AmGraph();
                graph2.type = "line";
                graph2.lineColor = "#27c5ff";
                graph2.bulletColor = "#FFFFFF";
                graph2.bulletBorderColor = "#27c5ff";
                graph2.bulletBorderThickness = 5;
                graph2.bulletBorderAlpha = 1;
                graph2.title = "回复";
                graph2.valueField = "expenses";
                graph2.lineThickness = 2;
                graph2.bullet = "round";
                graph2.fillAlphas = 0;
                graph2.balloonText = "[[uname]] [[title]] : [[value]] ";
                chart.addGraph(graph2);

                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                chart.creditsPosition = "top-right";

                // WRITE
                chart.write("chartdiv");
            });    	         
 
</script>

        
        <div><?php foreach($uers as $r){
			echo '['.$r['id'].'] '.$r['alias'].' ';
		}?></div>
        <div id="chartdiv" style="width:800px; height: 500px;"></div>
          </div>
       <script type="text/javascript">
            var chart2; 
            var chartData2 = [
               <?php 
			  $fbar  = array();
			  foreach($tags as $r){
			      $fbar[$r['tags']] =  $r['sum']; 
			  }
			  foreach($ftags as $k=>$v){
			   echo '{
                    "tag": "'.$k.'",
                    "tt": '.$v.', 
					"fb": '. (isset($fbar[$k])?$fbar[$k]:0).', 
                },';
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
                graph.title = "话题总数分类统计";
			    graph.type = "column";
				graph.lineAlpha = 0;
               
                graph.fillAlphas = 0.8; 
                graph.valueField = "tt";
                graph.balloonText = " [[category]]: [[value]]";
            //    graph.bullet = "round"; 
                chart2.addGraph(graph);
            
                // United Kingdom graph
                var graph = new AmCharts.AmGraph();
                graph.title = "发布话题分类统计";
                graph.valueField = "fb";
                graph.balloonText = " [[category]]: [[value]]";
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
        </script>
       <h3>信息发布标签分布</h3>
       <div id="chartdiv2" style="width: 100%; height: 400px;"></div> 
     <h3>话题统计</h3>
     <script type="text/javascript">
            var chartN; 
            var chartDataN = [
			<?php
			foreach($mtags as $r){
				echo '{
                    "country": '.$r['date'].'+"日",
                    "litres": '.$r['num'].',
                    "short": '.$r['num'].'+"条" 
                },'; 
			}			
			?> 
            ];

            AmCharts.ready(function () { 
                var chartN = new AmCharts.AmSerialChart();
                chartN.dataProvider = chartDataN;
                chartN.categoryField = "country";
                chartN.startDuration = 2; 
                chartN.balloon.color = "#000000";
 
                var categoryAxis = chart.categoryAxis;
                categoryAxis.gridAlpha = 0;
                categoryAxis.axisAlpha = 0;
                categoryAxis.labelsEnabled = false;

               
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.gridAlpha = 0;
                valueAxis.axisAlpha = 0;
                valueAxis.labelsEnabled = false;
                valueAxis.minimum = 0;
                chartN.addValueAxis(valueAxis);

                // GRAPH
                var graphN = new AmCharts.AmGraph();
                graphN.balloonText = "[[category]]: [[value]]";
                graphN.valueField = "litres";
                graphN.descriptionField = "short";
                graphN.type = "column";
                graphN.lineAlpha = 0;
                graphN.fillAlphas = 1;
                graphN.fillColors = ["#ffe78e", "#bf1c25"];
                graphN.labelText = "[[description]]";
                graphN.balloonText = "[[category]]: [[value]] 条";
                chartN.addGraph(graphN);

                chartN.creditsPosition = "top-right";
 
                chartN.write("chartdiv3");
            });
        </script>
       <div id="chartdiv3" style="width:100%; height: 400px;"></div> 
      
       </div>
    <div class="clear" style="clear:both;"></div>  
  </div> 
</div>
