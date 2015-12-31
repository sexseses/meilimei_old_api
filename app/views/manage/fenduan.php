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
                            <li><a href="<?php echo site_url('manage/tongji/fenduan2')?>">分段统计2</a></li></ul>
                        </div> 
                        <div class="clear" style="clear:both;"></div>
                        <div class="manage_yuyue" > <div class="manage_search"><form method="get">
						<ul> <li>时间<input name="yuyueDateStart" type="text" value="<?php  echo $cdate; ?>" class="datepicker"></li> <li><input name="yuyueDateEnd" type="text"  value="<?php  echo $edate; ?>" class="datepicker"></li><li><button type="submit">查询</button></li></ul> </form></div>
                        	<div class="manage_yuyue_form">
       <script type="text/javascript">
            var chartData = [ 
			<?php
			foreach($userReg as $r){
				echo ' {
                    "date": "'.$r['day'].'号",
                    "regnum": '.$r['sum'].',
                    "qarate": '.$r['rate'].' 
                },';
			}
			?> 
                
            ];
            var chart;

            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "date"; 
                chart.marginTop = 0;

                // AXES
                // category axis
                var categoryAxis = chart.categoryAxis;
                categoryAxis.parseDates = false; // as our data is date-based, we set parseDates to true
               
                categoryAxis.autoGridCount = false;
                categoryAxis.gridCount = 50;
                categoryAxis.gridAlpha = 0;
                categoryAxis.gridColor = "#000000";
                categoryAxis.axisColor = "#555555";
                // we want custom date formatting, so we change it in next line
           

                // as we have data of different units, we create two different value axes
                // qarate value axis            
                var durationAxis = new AmCharts.ValueAxis();
                durationAxis.title = "咨询比例";
                durationAxis.gridAlpha = 0.05;
                durationAxis.axisAlpha = 0;
                durationAxis.inside = true;
                // the following line makes this value axis to convert values to qarate
                // it tells the axis what qarate unit it should use. mm - minute, hh - hour...                
                durationAxis.qarate = "%";
                
                chart.addValueAxis(durationAxis);

                // regnum value axis 
                var distanceAxis = new AmCharts.ValueAxis();
                distanceAxis.title = "注册人数";
                distanceAxis.gridAlpha = 0;
                distanceAxis.position = "right";
                distanceAxis.inside = true;
                distanceAxis.unit = "人";
                distanceAxis.axisAlpha = 0;
                chart.addValueAxis(distanceAxis);

                // GRAPHS
                // qarate graph
                var durationGraph = new AmCharts.AmGraph();
                durationGraph.title = "注册用户中咨询的比例";
                durationGraph.valueField = "qarate";
                durationGraph.type = "line";
                durationGraph.valueAxis = durationAxis; // indicate which axis should be used
                durationGraph.lineColor = "#CC0000";
                durationGraph.balloonText = "[[value]]%";
                durationGraph.lineThickness = 1;
                durationGraph.legendValueText = "[[value]]%";
                durationGraph.bullet = "square";
                durationGraph.bulletBorderColor = "#CC0000";
                durationGraph.bulletBorderAlpha = 1;
                durationGraph.bulletBorderThickness = 1;
                chart.addGraph(durationGraph);

                // regnum graph
                var distanceGraph = new AmCharts.AmGraph();
                distanceGraph.valueField = "regnum";
                distanceGraph.title = "注册人数";
                distanceGraph.type = "column";
                distanceGraph.fillAlphas = 0.1;
                distanceGraph.valueAxis = distanceAxis; // indicate which axis should be used
                distanceGraph.balloonText = "[[value]] 人";
                distanceGraph.legendValueText = "[[value]] 人";
                distanceGraph.legendPeriodValueText = "total: [[value.sum]] 人";
                distanceGraph.lineColor = "#000000";
                distanceGraph.lineAlpha = 0;
                chart.addGraph(distanceGraph);

                // CURSOR                
                var chartCursor = new AmCharts.ChartCursor();
                chartCursor.zoomable = false;
                chartCursor.categoryBalloonDateFormat = "DD";
                chartCursor.cursorAlpha = 0;
                chart.addChartCursor(chartCursor);

                // LEGEND
                var legend = new AmCharts.AmLegend();
                legend.bulletType = "round";
                legend.equalWidths = false;
                legend.valueWidth = 120;
                legend.color = "#000000";
                legend.useGraphSettings = true;
                chart.addLegend(legend);

                // WRITE                                
                chart.write("userreg");
            });
        </script> <h3>新注册用户数与咨询比例</h3>
                            	 <div id="userreg" style="width:100%;height:200px;"></div> 
<script type="text/javascript">
            var newQS;
            var newQSData = [
			<?php foreach($qsTotal as $r){
				echo '{
                    "year": '.$r['day'].',
                    "val": '.$r['sum'].' ,
				    "val2": '.$r['qsum'].' 
                } ,';
			}
			?>  
            ]; 

            AmCharts.ready(function () {
                // SERIAL CHART  
                newQS = new AmCharts.AmSerialChart();
              
                newQS.dataProvider = newQSData;
                newQS.categoryField = "year";
                newQS.startDuration = 1;
                
                newQS.handDrawn = true;
                newQS.handDrawnScatter = 3;

                // AXES
                // category
                var categoryAxis = newQS.categoryAxis;
                categoryAxis.gridPosition = "start";

                // value
                var valueAxis = new AmCharts.ValueAxis();
                valueAxis.axisAlpha = 0;
                newQS.addValueAxis(valueAxis);

               
                // column graph
                var graph1 = new AmCharts.AmGraph();
                graph1.type = "<?php echo count($qsTotal)>2?'line':'column'?>";
                graph1.title = "用户数";
				<?php if(count($qsTotal)<=2):?>
				graph1.fillAlphas = 0.8;
				<?php endif ?>
                graph1.lineColor = "#66a330";
                graph1.valueField = "val";
                graph1.lineThickness = 2;
                graph1.bullet = "round";
                graph1.bulletBorderThickness = 3;
                graph1.bulletBorderColor = "#ddd";
                graph1.bulletBorderAlpha = 1;
                graph1.bulletColor = "#555";
                graph1.dashLengthField = "dashLengthLine";
                graph1.balloonText = " [[title]] : [[value]] ";
                newQS.addGraph(graph1);
 
                  // regnum graph
                 var graph2 = new AmCharts.AmGraph();
                graph2.type = "<?php echo count($qsTotal)>2?'line':'column'?>";
                graph2.title = "咨询数量";
				<?php if(count($qsTotal)<=2):?>
				graph2.fillAlphas = 0.8;
				<?php endif ?>
                graph2.lineColor = "#666";
                graph2.valueField = "val2";
                graph2.lineThickness = 2;
                graph2.bullet = "round";
                graph2.bulletBorderThickness = 3;
                graph2.bulletBorderColor = "#ddd";
                graph2.bulletBorderAlpha = 1;
                graph2.bulletColor = "#555";
                graph2.dashLengthField = "dashLengthLine";
                graph2.balloonText = " [[title]] : [[value]] ";
                newQS.addGraph(graph2);
                // LEGEND                
                var legend = new AmCharts.AmLegend();
                legend.useGraphSettings = true;
                newQS.addLegend(legend);

                // WRITE
                newQS.write("consuser");
            });
        </script> 
       <h3>新咨询用户数与咨询数量</h3>  <div id="consuser" style="width:100%;height:200px;"></div> 
          
                                <div class="clear" style="clear:both;"></div>
                            </div>
                             
                        </div>
                    </div>
    <div class="clear" style="clear:both;"></div> 
  </div><script> $(function() {
     $( ".datepicker" ).datepicker({ dateFormat: "yy-mm-dd" }).val();
  })  </script>
</div>
